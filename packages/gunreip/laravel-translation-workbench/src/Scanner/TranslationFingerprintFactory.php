<?php

namespace Gunreip\TranslationWorkbench\Scanner;

class TranslationFingerprintFactory
{
    /**
     * Central hashing entry point for translation-workbench scanner identifiers.
     *
     * Do not build scanner fingerprints/signatures with ad-hoc hash() calls in
     * individual commands or scanners. Add a named method here first so all
     * scanner components use the same normalization and hashing rules.
     *
     * @param  array<int, mixed>  $parts
     */
    public function signature(array $parts): string
    {
        return hash('sha256', implode('|', array_map(
            static fn(mixed $part): string => is_scalar($part) || $part === null
                ? (string) $part
                : json_encode($part, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            $parts,
        )));
    }

    public function sourceSignature(
        string $sourcePath,
        ?int $sourceLine,
        ?string $functionName,
        string $kind,
        ?string $suggestedKey,
        ?string $rawExpression,
        ?string $literalText,
        ?string $translationKey,
    ): string {
        return $this->signature([
            $sourcePath,
            (string) ($sourceLine ?? ''),
            $functionName ?? '',
            $kind,
            $suggestedKey ?? '',
            $this->normalizedExpression($rawExpression ?: ($literalText ?? $translationKey ?? $suggestedKey ?? '')),
        ]);
    }

    public function sourceFingerprint(string $sourcePath, ?int $sourceLine, ?string $rawExpression): string
    {
        return $this->signature([
            $sourcePath,
            (string) ($sourceLine ?? ''),
            $this->normalizedExpression($rawExpression ?? ''),
        ]);
    }

    public function expressionFingerprint(?string $rawExpression): string
    {
        return hash('sha256', $this->normalizedExpression($rawExpression ?? ''));
    }

    public function semanticFingerprint(string $kind, ?string $functionName, ?string $semanticValue): string
    {
        return $this->signature([
            $kind,
            $functionName ?? '',
            $this->normalizedExpression($semanticValue ?? ''),
        ]);
    }

    public function entryFingerprint(
        string $kind,
        ?string $functionName,
        ?string $literalText,
        ?string $literalTextSuggested,
        ?string $existingKey,
        ?string $translationKey,
        ?string $dynamicScope,
    ): string {
        return $this->signature([
            $kind,
            $functionName ?? '',
            $literalText ?? '',
            $literalTextSuggested ?? '',
            $existingKey ?? '',
            $translationKey ?? '',
            $dynamicScope ?? '',
        ]);
    }

    public function normalizedExpression(string $value): string
    {
        return trim((string) preg_replace('/\s+/', ' ', $value));
    }
}
