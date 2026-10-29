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

    /** Only unique, hash-current English file evidence can explain an existing key. */
    public function languageEvidence(PDO $db): array
    {
        $rows = $db->query("SELECT e.full_name,e.value,e.path,s.sha256 FROM published_value_evidence e
            JOIN published_value_sources s USING(baseline_hash,path,locale) WHERE e.locale='en'")->fetchAll(PDO::FETCH_ASSOC);
        $groups = [];
        $hashes = [];
        foreach ($rows as $row) {
            $path = $row['path'];
            if (str_starts_with($path, '/') || in_array('..', explode('/', $path), true)) {
                continue;
            }
            $hashes[$path] ??= @hash_file('sha256', base_path($path));
            if ($hashes[$path] === $row['sha256']) {
                $groups[$row['full_name']][] = ['value' => $row['value'], 'provenance' => [
                    'key' => $row['full_name'], 'locale' => 'en', 'path' => $path, 'sha256' => $row['sha256'],
                ]];
            }
        }
        $result = [];
        foreach ($groups as $key => $evidence) {
            if (count($evidence) === 1) {
                $result[$key] = $evidence[0];
            }
        }

        return $result;
    }
}
