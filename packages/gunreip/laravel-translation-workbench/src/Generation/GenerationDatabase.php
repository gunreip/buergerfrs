<?php

namespace Gunreip\TranslationWorkbench\Generation;

use Gunreip\TranslationWorkbench\Support\WorkingGenerationSchema;
use Illuminate\Support\Facades\DB;
use PDO;
use RuntimeException;

/** A separate connection prevents generation queries from falling back to public. */
final class GenerationDatabase
{
    public const SCHEMA = 'current_generation_v2';

    public function connection(): PDO
    {
        $config = DB::connection()->getConfig();
        if ($config['driver'] !== 'pgsql') {
            throw new RuntimeException('The working generation requires PostgreSQL.');
        }
        $config['search_path'] = self::SCHEMA.',pg_catalog';
        $config['schema'] = self::SCHEMA.',pg_catalog';
        config(['database.connections.workbench_generation' => $config]);

        return DB::connection('workbench_generation')->getPdo();
    }

    public function verify(PDO $db): array
    {
        $generation = $db->query('SELECT generation FROM '.self::SCHEMA.'.workbench_generation')->fetchColumn();

        return (new WorkingGenerationSchema)->verify($db, $db->query('SELECT current_database()')->fetchColumn(), self::SCHEMA, $generation);
    }

    /** Only unique English values verified in current files can explain an existing key. */
    public function languageEvidence(PDO $db): array
    {
        $rows = $db->query("SELECT e.full_name,e.value,e.path,s.sha256 FROM published_value_evidence e
            JOIN published_value_sources s USING(baseline_hash,path,locale) WHERE e.locale='en'")->fetchAll(PDO::FETCH_ASSOC);

        // Read completed receipts, never drafts or merely prepared publications.
        $query = $db->prepare('SELECT published.full_name,published.value,file.key AS path FROM ('.PublishedWorklist::CURRENT_PUBLICATIONS.
            ") published JOIN translation_workbench_publication_runs p ON p.id=published.run
            CROSS JOIN LATERAL jsonb_each(p.plan::jsonb->'files') file
            WHERE published.locale='en' AND published.namespace='' AND published.working_state<>2
                AND p.plan::jsonb->>'directory'=?
                AND file.key='en/'||split_part(published.full_name,'.',1)||'.php'");
        $directory = realpath(lang_path()) ?: lang_path();
        $query->execute([$directory]);
        $root = rtrim(realpath(base_path()) ?: base_path(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if (str_starts_with($directory.DIRECTORY_SEPARATOR, $root)) {
            $prefix = substr($directory.DIRECTORY_SEPARATOR, strlen($root));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $row['path'] = $prefix.$row['path'];
                $rows[] = $row;
            }
        }

        return (new CurrentLanguageEvidence)->resolve($rows, base_path());
    }
}
