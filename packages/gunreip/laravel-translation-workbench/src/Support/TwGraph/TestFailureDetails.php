<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

/** Shared presentation data for the diagnostics page and standalone HTML report. */
final class TestFailureDetails
{
    public static function fromCheck(array $check): array
    {
        $failures = array_merge((array) data_get($check, 'parsed_output.failures', []), (array) data_get($check, 'parsed_output.error_details', []));
        if ($failures === []) {
            $output = self::plainText((string) ($check['output'] ?? __('No failure details available.')));
            $lines = preg_split('/\R/', trim($output));

            return [['message' => $lines[0] ?: __('No failure details available.'), 'test' => null, 'location' => '',
                'expected' => null, 'actual' => null, 'details' => $output]];
        }

        return array_map(static function (array $failure): array {
            $message = self::plainText((string) ($failure['message'] ?? __('No failure message available.')));
            // Only separate explicit trace markers; preserve multiline assertion messages and diffs.
            $parts = preg_split('/\R(?=Stack trace:|#0\s)/', $message, 2);
            $details = count($parts) > 1 ? $message : '';
            if (isset($failure['trace'])) {
                $details .= ($details !== '' ? PHP_EOL : '').self::value($failure['trace']);
            }
            $location = (string) ($failure['file'] ?? '');
            if ($location !== '' && isset($failure['line'])) {
                $location .= ':'.$failure['line'];
            }

            return [
                'message' => $parts[0],
                'test' => $failure['test'] ?? __('Failed test'),
                'location' => $location,
                'expected' => array_key_exists('expected', $failure) ? self::value($failure['expected']) : null,
                'actual' => array_key_exists('actual', $failure) ? self::value($failure['actual']) : null,
                'details' => $details,
            ];
        }, $failures);
    }

    public static function plainText(string $value): string
    {
        // Strip actual terminal control sequences, not literal bracketed text in assertions.
        return (string) preg_replace('/\x1B\[[0-?]*[ -\/]*[@-~]/', '', $value);
    }

    private static function value(mixed $value): string
    {
        return is_string($value) ? self::plainText($value) : (string) json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
