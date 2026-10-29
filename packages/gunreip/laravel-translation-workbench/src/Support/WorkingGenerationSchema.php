<?php

namespace Gunreip\TranslationWorkbench\Support;

use PDO;
use RuntimeException;
use Throwable;

/** Explicit installation into a new schema; never registered as an automatic migration. */
final class WorkingGenerationSchema
{
    public const VERSION = 2;

    private const FILES = [
        'v1/baseline.sql', 'v1/capture-ledger.sql', 'v1/source-roles.sql', 'v1/source-arguments.sql',
        'v1/existing-keys.sql', 'v1/published-values.sql', 'v1/key-correction-proposals.sql',
        'v1/key-correction-applications.sql', 'v1/corrected-value-drafts.sql', 'v2/name-inventory.sql',
    ];

    public function install(PDO $db, string $database, string $schema, string $generation): array
    {
        $this->validate($db, $database, $schema, $generation);
        $db->beginTransaction();
        try {
            $lock = $db->prepare('SELECT pg_catalog.pg_advisory_xact_lock(pg_catalog.hashtextextended(?, 0))');
            $lock->execute(['workbench-generation:'.$schema]);
            $exists = $db->prepare('SELECT 1 FROM pg_catalog.pg_namespace WHERE nspname=?');
            $exists->execute([$schema]);
            if ($exists->fetchColumn()) {
                throw new RuntimeException('Generation schema already exists; no adoption or overwrite is allowed');
            }
            $db->exec('CREATE SCHEMA "'.$schema.'"');
            $db->exec('SET LOCAL search_path TO "'.$schema.'", pg_catalog, pg_temp');
            $manifest = [];
            foreach (self::FILES as $file) {
                $sql = file_get_contents($this->directory().'/'.$file);
                if ($sql === false) {
                    throw new RuntimeException('Generation schema source is unreadable: '.$file);
                }
                $manifest[$file] = hash('sha256', $sql);
                $db->exec($sql);
            }
            $db->exec('CREATE TABLE workbench_generation (
                singleton boolean PRIMARY KEY DEFAULT true CHECK(singleton),
                generation uuid NOT NULL UNIQUE, version integer NOT NULL,
                manifest jsonb NOT NULL, catalog_sha256 text NOT NULL,
                installed_at timestamptz NOT NULL DEFAULT now()
            );
            CREATE TRIGGER immutable_generation BEFORE UPDATE OR DELETE ON workbench_generation
                FOR EACH ROW EXECUTE FUNCTION tw_immutable()');
            $db->exec('SET LOCAL search_path TO pg_catalog');
            $catalog = $this->catalogHash($db, $schema);
            $insert = $db->prepare('INSERT INTO "'.$schema.'".workbench_generation
                (generation,version,manifest,catalog_sha256) VALUES (?,?,?::jsonb,?)');
            $insert->execute([$generation, self::VERSION, json_encode($manifest, JSON_THROW_ON_ERROR), $catalog]);
            $db->commit();

            return ['generation' => $generation, 'version' => self::VERSION, 'schema' => $schema, 'catalog_sha256' => $catalog];
        } catch (Throwable $error) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $error;
        }
    }

    public function verify(PDO $db, string $database, string $schema, string $generation): array
    {
        $this->validate($db, $database, $schema, $generation);
        $db->beginTransaction();
        try {
            $db->exec('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ, READ ONLY');
            $db->exec('SET LOCAL search_path TO pg_catalog');
            $query = $db->query('SELECT generation,version,manifest,catalog_sha256 FROM "'.$schema.'".workbench_generation');
            $rows = $query->fetchAll(PDO::FETCH_ASSOC);
            $manifest = [];
            foreach (self::FILES as $file) {
                $hash = hash_file('sha256', $this->directory().'/'.$file);
                if ($hash === false) {
                    throw new RuntimeException('Generation schema source is unreadable: '.$file);
                }
                $manifest[$file] = $hash;
            }
            if (count($rows) !== 1 || $rows[0]['generation'] !== $generation
                || (int) $rows[0]['version'] !== self::VERSION
                || json_decode($rows[0]['manifest'], true, flags: JSON_THROW_ON_ERROR) != $manifest) {
                throw new RuntimeException('Generation identity, version or SQL manifest differs');
            }
            $catalog = $this->catalogHash($db, $schema);
            if (! hash_equals($rows[0]['catalog_sha256'], $catalog)) {
                throw new RuntimeException('Installed generation schema differs from its recorded structure');
            }
            $db->commit();

            return ['generation' => $generation, 'version' => self::VERSION, 'schema' => $schema, 'catalog_sha256' => $catalog];
        } catch (Throwable $error) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $error;
        }
    }

    private function validate(PDO $db, string $database, string $schema, string $generation): void
    {
        if ($db->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'pgsql'
            || $db->getAttribute(PDO::ATTR_ERRMODE) !== PDO::ERRMODE_EXCEPTION
            || $db->inTransaction() || trim($database) === ''
            || preg_match('/^[a-z_][a-z0-9_]{0,62}$/D', $schema) !== 1
            || in_array($schema, ['public', 'information_schema'], true) || str_starts_with($schema, 'pg_')
            || preg_match('/^[a-f0-9]{8}-(?:[a-f0-9]{4}-){3}[a-f0-9]{12}$/D', $generation) !== 1) {
            throw new RuntimeException('Explicit PostgreSQL target, separate schema and generation UUID are required outside a transaction');
        }
        if ($db->query('SELECT pg_catalog.current_database()')->fetchColumn() !== $database) {
            throw new RuntimeException('Database differs from the requested generation target');
        }
    }

    private function directory(): string
    {
        return dirname(__DIR__, 2).'/database/generations';
    }

    /** Structural definitions only: no row contents, sequence counters or local object OIDs. */
    private function catalogHash(PDO $db, string $schema): string
    {
        $queries = [
            'relations' => 'SELECT c.relname,c.relkind,c.relrowsecurity,c.relforcerowsecurity
                FROM pg_class c JOIN pg_namespace n ON n.oid=c.relnamespace
                WHERE n.nspname=? ORDER BY c.relname',
            'columns' => "SELECT c.relname,a.attnum,a.attname,format_type(a.atttypid,a.atttypmod) AS type,
                a.attnotnull,a.attidentity,a.attgenerated,pg_get_expr(d.adbin,d.adrelid) AS default_expression
                FROM pg_attribute a JOIN pg_class c ON c.oid=a.attrelid
                JOIN pg_namespace n ON n.oid=c.relnamespace
                LEFT JOIN pg_attrdef d ON d.adrelid=c.oid AND d.adnum=a.attnum
                WHERE n.nspname=? AND c.relkind IN ('r','p') AND a.attnum>0 AND NOT a.attisdropped
                ORDER BY c.relname,a.attnum",
            'constraints' => 'SELECT c.relname,k.conname,k.convalidated,pg_get_constraintdef(k.oid,false) AS definition
                FROM pg_constraint k JOIN pg_class c ON c.oid=k.conrelid
                JOIN pg_namespace n ON n.oid=c.relnamespace WHERE n.nspname=? ORDER BY c.relname,k.conname',
            'indexes' => 'SELECT c.relname,i.indisvalid,i.indisready,pg_get_indexdef(i.indexrelid) AS definition
                FROM pg_index i JOIN pg_class c ON c.oid=i.indexrelid
                JOIN pg_namespace n ON n.oid=c.relnamespace WHERE n.nspname=? ORDER BY c.relname',
            // Internal FK trigger names contain installation-specific OIDs. Preserve their
            // constraint identity, enabled state and full behavior, not the generated suffix.
            'triggers' => "SELECT c.relname,
                CASE WHEN t.tgisinternal THEN k.conrelid::regclass::text || ':' || k.conname::text || ':' || p.proname::text
                    ELSE t.tgname::text END AS trigger_identity,
                t.tgenabled,
                CASE WHEN t.tgisinternal THEN regexp_replace(pg_get_triggerdef(t.oid,false),
                    'RI_ConstraintTrigger_[ac]_[0-9]+', 'RI_ConstraintTrigger', 'g')
                    ELSE pg_get_triggerdef(t.oid,false) END AS definition
                FROM pg_trigger t JOIN pg_class c ON c.oid=t.tgrelid
                JOIN pg_namespace n ON n.oid=c.relnamespace
                JOIN pg_proc p ON p.oid=t.tgfoid
                LEFT JOIN pg_constraint k ON k.oid=t.tgconstraint
                WHERE n.nspname=? ORDER BY c.relname,trigger_identity",
            'functions' => 'SELECT p.proname,pg_get_functiondef(p.oid) AS definition
                FROM pg_proc p JOIN pg_namespace n ON n.oid=p.pronamespace
                WHERE n.nspname=? ORDER BY p.proname,pg_get_function_identity_arguments(p.oid)',
            'sequences' => 'SELECT c.relname,s.seqstart,s.seqincrement,s.seqmax,s.seqmin,s.seqcache,s.seqcycle
                FROM pg_sequence s JOIN pg_class c ON c.oid=s.seqrelid
                JOIN pg_namespace n ON n.oid=c.relnamespace WHERE n.nspname=? ORDER BY c.relname',
        ];
        $snapshot = [];
        foreach ($queries as $name => $sql) {
            $query = $db->prepare($sql);
            $query->execute([$schema]);
            $snapshot[$name] = $query->fetchAll(PDO::FETCH_ASSOC);
        }

        return hash('sha256', json_encode($snapshot, JSON_THROW_ON_ERROR));
    }
}
