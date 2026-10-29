<?php

namespace Gunreip\TranslationWorkbench\Generation;

/** Independent key ID, key text and language filters, combined with AND. */
final class TranslationTaskFilter
{
    public static function build(string $search, string $keyId, string $locale = '', string $prefix = ''): array
    {
        $conditions = [];
        $parameters = [];
        if (trim($keyId) !== '') {
            $keyId = trim($keyId);
            if (! ctype_digit($keyId) || strlen($keyId) > 18) {
                $conditions[] = 'FALSE';
            } else {
                $conditions[] = $prefix.'key_id=?';
                $parameters[] = $keyId;
            }
        }
        if (trim($search) !== '') {
            $conditions[] = $prefix.'full_name ILIKE ?';
            $parameters[] = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], trim($search)).'%';
        }
        if (trim($locale) !== '') {
            $conditions[] = $prefix.'locale=?';
            $parameters[] = trim($locale);
        }
        return [implode(' AND ', $conditions), $parameters];
    }
}
