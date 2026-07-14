<?php

namespace Gunreip\TranslationWorkbench\Scanner;

final class DiscoveredTranslation
{
    /**
     * @param  array<string, mixed>  $meta
     */
    public function __construct(
        public readonly string $kind,
        public readonly string $sourceType,
        public readonly string $sourcePath,
        public readonly ?int $sourceLine,
        public readonly ?string $functionName,
        public readonly ?string $rawExpression,
        public readonly ?string $literalText,
        public readonly ?string $literalTextSuggested,
        public readonly ?string $translationKey,
        public readonly ?string $translationKeySource,
        public readonly ?string $existingKey,
        public readonly ?string $suggestedKey,
        public readonly ?string $namespace,
        public readonly ?string $group,
        public readonly string $sourceSignature,
        public readonly string $fingerprint,
        public readonly string $sourceFingerprint,
        public readonly string $expressionFingerprint,
        public readonly string $semanticFingerprint,
        public readonly array $meta = [],
        public readonly ?string $entryType = null,
        public readonly ?string $candidateType = null,
        public readonly ?string $candidateReason = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toEntryAttributes(): array
    {
        return [
            'fingerprint' => $this->fingerprint,
            'source_signature' => $this->sourceSignature,
            'source_fingerprint' => $this->sourceFingerprint,
            'expression_fingerprint' => $this->expressionFingerprint,
            'semantic_fingerprint' => $this->semanticFingerprint,
            'kind' => $this->kind,
            'entry_type' => $this->entryType,
            'candidate_type' => $this->candidateType,
            'candidate_reason' => $this->candidateReason,
            'source_type' => $this->sourceType,
            'source_path' => $this->sourcePath,
            'source_line' => $this->sourceLine,
            'function_name' => $this->functionName,
            'raw_expression' => $this->rawExpression,
            'literal_text' => $this->literalText,
            'literal_text_suggested' => $this->literalTextSuggested,
            'translation_key' => $this->translationKey,
            'translation_key_source' => $this->translationKeySource,
            'existing_key' => $this->existingKey,
            'suggested_key' => $this->suggestedKey,
            'namespace' => $this->namespace,
            'group' => $this->group,
            'meta' => $this->meta,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toOccurrenceAttributes(): array
    {
        return [
            'source_signature' => $this->sourceSignature,
            'source_type' => $this->sourceType,
            'source_path' => $this->sourcePath,
            'source_line' => $this->sourceLine,
            'function_name' => $this->functionName,
            'raw_expression' => $this->rawExpression,
            'suggested_key' => $this->suggestedKey,
            'literal_text_suggested' => $this->literalTextSuggested,
            'meta' => $this->meta,
        ];
    }
}
