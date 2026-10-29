<?php

namespace Tests\Support\TranslationWorkbench;

use Gunreip\TranslationWorkbench\Generation\GenerationPublisher;
use PDO;
use RuntimeException;

/** Persistence experiment, restricted to the runner's own temporary cluster directory. */
final class PostgresPublisher extends GenerationPublisher
{
    public function __construct(string $cluster, ?PDO $restoredCopy = null)
    {
        if ($restoredCopy === null) {
            $db = (new PostgresBaseline($cluster, 'publication-test'))->db;
            $directory = realpath($cluster).'/publication-files';
        } else {
            $resolved = realpath($cluster);
            $database = $restoredCopy->query('SELECT current_database()')->fetchColumn();
            if (! $resolved || dirname($resolved) !== realpath(sys_get_temp_dir())
                || ! preg_match('/^tw-restore-[a-f0-9]{16}$/D', basename($resolved))
                || ! preg_match('/^application_[a-f0-9]{12}$/D', $database)
                || $restoredCopy->query('SHOW data_directory')->fetchColumn() !== $resolved.'/data'
                || $restoredCopy->query('SHOW listen_addresses')->fetchColumn() !== '') {
                throw new RuntimeException('Only an isolated restored application copy is allowed');
            }
            $db = $restoredCopy;
            $schema = $restoredCopy->query('SELECT current_schema()')->fetchColumn();
            if (! is_string($schema) || ! preg_match('/^[a-z][a-z0-9_]{0,62}$/D', $schema)) {
                throw new RuntimeException('Invalid publication schema');
            }
            $directory = $resolved.'/publication-'.$database.($schema === 'public' ? '' : '-'.$schema);
        }
        if (is_link($directory)) {
            throw new RuntimeException('Symlink root');
        }
        if (! is_dir($directory)) {
            mkdir($directory, 0700);
        }
        parent::__construct($db, $directory);
    }
}
