<?php

// app/Support/ActivityLog/ConsoleActivityContext.php

namespace App\Support\ActivityLog;

use Illuminate\Console\Command;

class ConsoleActivityContext
{
    /**
     * Build a reusable activity_log properties context for console commands.
     *
     * @return array<string, mixed>
     */
    public static function forCommand(Command $command): array
    {
        return [
            'command' => $command->getName(),
            'actor' => self::actor(),
        ];
    }

    /**
     * Merge caller supplied properties with the default console command context.
     *
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    public static function merge(Command $command, array $properties = []): array
    {
        return array_replace_recursive(
            self::forCommand($command),
            $properties,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function actor(): array
    {
        return [
            'type' => app()->runningInConsole() ? 'terminal' : 'web',
            'terminal_user' => self::terminalUserName(),
            'hostname' => gethostname() ?: null,
            'php_sapi' => PHP_SAPI,
            'cwd' => getcwd() ?: null,
        ];
    }

    private static function terminalUserName(): ?string
    {
        if (function_exists('posix_geteuid') && function_exists('posix_getpwuid')) {
            $userInfo = posix_getpwuid(posix_geteuid());

            if (is_array($userInfo) && isset($userInfo['name'])) {
                $userName = self::stringOrNull($userInfo['name']);

                if ($userName !== null) {
                    return $userName;
                }
            }
        }

        foreach (['USER', 'LOGNAME', 'USERNAME'] as $environmentKey) {
            $userName = self::stringOrNull(getenv($environmentKey));

            if ($userName !== null) {
                return $userName;
            }
        }

        return self::stringOrNull(get_current_user());
    }

    private static function stringOrNull(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value !== '' ? $value : null;
    }
}
