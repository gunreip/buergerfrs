<?php

// app/Support/Fallbacks/FallbackReporter.php

namespace App\Support\Fallbacks;

use App\Models\FallbackReport;
use Illuminate\Support\Facades\Log;
use Throwable;

class FallbackReporter
{
    public function report(
        string $type,
        string $key,
        ?string $fallback = null,
        array $context = [],
    ): ?FallbackReport {
        $type = trim($type);
        $key = trim($key);
        $fallback = $fallback !== null ? trim($fallback) : null;

        $fingerprintContext = $this->normalizeContext($context);
        $context = $this->normalizeContext([
            ...$fingerprintContext,
            'runtime' => $this->runtimeContext(),
        ]);

        if ($type === '' || $key === '') {
            return null;
        }

        $fingerprint = $this->fingerprint($type, $key, $fallback, $fingerprintContext);

        try {
            $report = FallbackReport::query()
                ->where('fingerprint', $fingerprint)
                ->where('reviewed', false)
                ->first();

            if ($report) {
                $report->forceFill([
                    'count' => $report->count + 1,
                    'last_seen_at' => now(),
                    'context' => $context,
                ])->save();

                return $report;
            }

            return FallbackReport::query()->create([
                'type' => $type,
                'key' => $key,
                'fallback' => $fallback,
                'fingerprint' => $fingerprint,
                'context' => $context,
                'count' => 1,
                'first_seen_at' => now(),
                'last_seen_at' => now(),
                'reviewed' => false,
            ]);
        } catch (Throwable $exception) {
            Log::warning('Fallback report could not be written.', [
                'type' => $type,
                'key' => $key,
                'fallback' => $fallback,
                'context' => $context,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    private function fingerprint(
        string $type,
        string $key,
        ?string $fallback,
        array $context,
    ): string {
        return hash('sha256', json_encode([
            'type' => $type,
            'key' => $key,
            'fallback' => $fallback,
            'context' => $context,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
    }

    private function runtimeContext(): array
    {
        return [
            'route_name' => request()->route()?->getName(),
            'path' => request()->path(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
        ];
    }

    private function normalizeContext(array $context): array
    {
        ksort($context);

        foreach ($context as $key => $value) {
            if (is_array($value)) {
                $context[$key] = $this->normalizeContext($value);

                continue;
            }

            if (is_bool($value) || is_int($value) || is_float($value) || $value === null) {
                continue;
            }

            $context[$key] = (string) $value;
        }

        return $context;
    }
}
