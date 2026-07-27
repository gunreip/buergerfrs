<?php

namespace Gunreip\TranslationWorkbench\Console\Concerns;

use Throwable;

trait ConfirmsTranslationWorkbenchTruncate
{
    /**
     * Central truncate guard for Translation Workbench console commands.
     *
     * A truncate decision must not be changed silently per subcommand. The
     * orchestrator asks once and passes the force option to child commands.
     * Direct subcommand usage asks here before any destructive write happens.
     *
     * @param  array<string, mixed>  $properties
     */
    protected function confirmTranslationWorkbenchTruncate(
        bool $truncate,
        bool $dryRun,
        string $warning,
        string $forceOption = 'force-truncate',
        array $properties = [],
    ): bool {
        if (! $truncate || $dryRun) {
            return true;
        }

        if ((bool) $this->option($forceOption)) {
            $this->recordTranslationWorkbenchActivity(
                'translation_workbench.truncate_confirmed',
                'Translation Workbench truncate confirmed',
                array_replace($properties, ['forced' => true]),
            );

            return true;
        }

        $this->newLine();
        $this->components->warn($warning);

        $confirmed = $this->confirm('Continue and truncate Translation Workbench tables?', false);

        $this->recordTranslationWorkbenchActivity(
            $confirmed ? 'translation_workbench.truncate_confirmed' : 'translation_workbench.truncate_cancelled',
            $confirmed ? 'Translation Workbench truncate confirmed' : 'Translation Workbench truncate cancelled',
            array_replace($properties, ['forced' => false]),
        );

        return $confirmed;
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    protected function recordTranslationWorkbenchActivity(string $event, string $description, array $properties = []): void
    {
        if (! function_exists('activity')) {
            return;
        }

        try {
            activity('translation-workbench')
                ->event($event)
                ->withProperties(array_replace_recursive([
                    'command' => $this->getName(),
                    'actor' => [
                        'type' => app()->runningInConsole() ? 'terminal' : 'web',
                        'terminal_user' => $this->translationWorkbenchTerminalUserName(),
                        'hostname' => gethostname() ?: null,
                        'php_sapi' => PHP_SAPI,
                        'cwd' => getcwd() ?: null,
                    ],
                ], $properties))
                ->log($description);
        } catch (Throwable) {
            //
        }
    }

    private function translationWorkbenchTerminalUserName(): ?string
    {
        if (function_exists('posix_geteuid') && function_exists('posix_getpwuid')) {
            $userInfo = posix_getpwuid(posix_geteuid());

            if (is_array($userInfo) && isset($userInfo['name']) && is_string($userInfo['name'])) {
                $userName = trim($userInfo['name']);

                if ($userName !== '') {
                    return $userName;
                }
            }
        }

        foreach (['USER', 'LOGNAME', 'USERNAME'] as $environmentKey) {
            $userName = getenv($environmentKey);

            if (is_string($userName) && trim($userName) !== '') {
                return trim($userName);
            }
        }

        $userName = get_current_user();

        return is_string($userName) && trim($userName) !== '' ? trim($userName) : null;
    }
}
