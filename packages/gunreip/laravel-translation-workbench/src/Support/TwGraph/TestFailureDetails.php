<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph;

/** Shared presentation data for the diagnostics page and standalone HTML report. */
final class TestFailureDetails
{
    public static function summary(iterable $checks): string
    {
        $entries = [];
        foreach ($checks as $check) {
            if (($check['status'] ?? null) !== 'failed') {
                continue;
            }
            foreach (self::fromCheck($check) as $failure) {
                $message = preg_split('/\R/', trim($failure['message']))[0];
                $entries[] = implode(PHP_EOL, array_filter([
                    'FAIL · '.self::plainText((string) ($check['name'] ?? __('Check'))),
                    $failure['test'],
                    $failure['location'],
                    \Illuminate\Support\Str::limit($message, 500),
                ], static fn ($value): bool => $value !== null && $value !== ''));
            }
        }

        return implode(PHP_EOL.PHP_EOL, $entries);
    }

    public static function fromCheck(array $check): array
    {
        $failures = array_merge((array) data_get($check, 'parsed_output.failures', []), (array) data_get($check, 'parsed_output.error_details', []));
        if ($failures === []) {
            $output = self::plainText((string) ($check['output'] ?? ''));
            // Artisan's human-readable Pest output has no structured failure array.
            preg_match_all('/^\h*FAILED\h+([^\r\n]+)\R(.*?)(?=^\h*FAILED\h|^\h*Tests:|\z)/ms', $output, $blocks, PREG_SET_ORDER);
            foreach ($blocks as $block) {
                $body = trim($block[2]);
                preg_match('/^\h*at\h+(.+?):(\d+)\h*$/m', $body, $location);
                preg_match('/^\h*(To contain:|Failed asserting[^\r\n]*|[^\r\n]*(?:Exception|Error):)[^\r\n]*/m', $body, $message);
                $failures[] = [
                    'test' => trim($block[1]),
                    'message' => isset($message[0]) ? trim($message[0]) : (preg_split('/\R/', $body)[0] ?: __('No failure message available.')),
                    'file' => $location[1] ?? '',
                    'line' => isset($location[2]) ? (int) $location[2] : null,
                    'trace' => $body,
                ];
            }
        }
        if ($failures === []) {
            $output = self::plainText((string) ($check['output'] ?? __('No failure details available.')));
            $lines = array_values(array_filter(preg_split('/\R/', trim($output)), static fn (string $line): bool => trim($line) !== '' && ! preg_match('/^\h*(?:PASS\b|✓)/u', $line)));

            return [['message' => ($lines[0] ?? '') ?: __('No failure details available.'), 'test' => null, 'location' => '',
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
