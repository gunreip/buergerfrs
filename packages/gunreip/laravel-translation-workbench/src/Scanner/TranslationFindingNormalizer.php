<?php

namespace Gunreip\TranslationWorkbench\Scanner;

use Illuminate\Support\Str;

class TranslationFindingNormalizer
{
    /**
     * Central normalization helpers for scanner findings.
     *
     * Keep expression, dynamic argument, scope and literal-suggestion rules here.
     * Scanner commands should call these methods instead of reimplementing
     * normalizers locally.
     */
    public function normalizedExpression(string $value): string
    {
        return trim((string) preg_replace('/\s+/', ' ', $value));
    }

    public function normalizedDynamicArgument(string $argument): string
    {
        $argument = trim($argument);
        $argument = rtrim($argument, " \t\n\r\0\x0B)");

        return $this->normalizedExpression($argument);
    }

    public function isDynamicArgumentCandidate(string $argument): bool
    {
        if ($argument === '') {
            return false;
        }

        if (str_starts_with($argument, "'") || str_starts_with($argument, '"')) {
            return false;
        }

        if (preg_match('/^(true|false|null|\d+)$/i', $argument)) {
            return false;
        }

        return str_contains($argument, '$');
    }

    public function dynamicScopeFromArgument(string $argument): string
    {
        $scope = preg_replace('/[^A-Za-z0-9_.$>\[\]\'"-]+/', '_', $argument) ?? $argument;
        $scope = str_replace(['$', '->', '[', ']', "'", '"'], ['', '.', '.', '', '', ''], $scope);
        $scope = trim($scope, '._-');

        return $scope !== '' ? $scope : 'expression';
    }

    public function dynamicKeyNameFromArgument(string $argument): string
    {
        if (preg_match('/^\$(?P<name>[A-Za-z_][A-Za-z0-9_]*)\s*\[/u', $argument, $match)) {
            return (string) $match['name'];
        }

        if (preg_match('/\$[A-Za-z_][A-Za-z0-9_]*->(?P<property>[A-Za-z_][A-Za-z0-9_]*)/u', $argument, $match)) {
            return (string) $match['property'];
        }

        if (preg_match('/^\$(?P<name>[A-Za-z_][A-Za-z0-9_]*)\b/u', $argument, $match)) {
            return (string) $match['name'];
        }

        $scope = $this->dynamicScopeFromArgument($argument);
        $segments = array_values(array_filter(explode('.', $scope)));

        return (string) (end($segments) ?: $scope);
    }

    public function scopeFromOptionsVariable(string $optionsVariable): string
    {
        $scope = preg_replace('/Options$/', '', $optionsVariable) ?: $optionsVariable;

        return Str::snake($scope) . '_options';
    }

    public function literalTextSuggestedFromDynamicKeyName(string $keyName): ?string
    {
        $suggestion = trim($keyName);
        $suggestion = preg_replace('/([a-z])([A-Z])/', '$1 $2', $suggestion) ?? $suggestion;
        $suggestion = trim((string) preg_replace('/[_-]+/', ' ', $suggestion));
        $suggestion = trim((string) preg_replace('/\s+/', ' ', $suggestion));
        $suggestion = strtolower($suggestion);

        return $suggestion !== '' ? $suggestion : null;
    }

    public function literalTextSuggestedFromScope(string $scope): string
    {
        $suggestion = preg_replace('/_options$/', '', $scope) ?? $scope;
        $suggestion = str_replace(['_', '-'], ' ', $suggestion);
        $suggestion = preg_replace('/\s+/', ' ', $suggestion) ?? $suggestion;

        return trim($suggestion);
    }

    public function literalTextSuggestedFromRawExpression(?string $rawExpression): ?string
    {
        if ($rawExpression === null || $rawExpression === '') {
            return null;
        }

        if (! preg_match('/\(\s*(?P<quote>[\'"])(?P<value>(?:\\\\.|(?!\k<quote>).)*)\k<quote>/su', $rawExpression, $match)) {
            return null;
        }

        $value = stripcslashes((string) $match['value']);

        return $this->literalTextSuggestedFromTranslationKey($value);
    }

    public function literalTextSuggestedFromTranslationKey(?string $translationKey): ?string
    {
        $translationKey = trim((string) $translationKey, " \t\n\r\0\x0B.");

        if ($translationKey === '') {
            return null;
        }

        $segments = array_values(array_filter(
            explode('.', $translationKey),
            static fn(string $segment): bool => $segment !== '',
        ));

        $suggestion = (string) (end($segments) ?: $translationKey);
        $suggestion = trim((string) preg_replace('/[_-]+/', ' ', $suggestion));
        $suggestion = trim((string) preg_replace('/\s+/', ' ', $suggestion));

        return $suggestion !== '' ? $suggestion : null;
    }

    public function looksLikeTranslationKey(string $value): bool
    {
        if (str_contains($value, ' ')) {
            return false;
        }

        return (bool) preg_match('/^[a-z0-9_.-]+$/i', $value)
            && str_contains($value, '.');
    }
}
