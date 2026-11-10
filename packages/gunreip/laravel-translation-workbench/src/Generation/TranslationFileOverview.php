<?php

namespace Gunreip\TranslationWorkbench\Generation;

use RuntimeException;

/** Read-only file evidence; presence alone is not a publication receipt. */
final class TranslationFileOverview
{
    public function inspect(string $root, string $key, array $locales, array $revisions, array $publications): array
    {
        if (! preg_match('/^[a-z0-9_-]+\.[a-zA-Z0-9_.-]+$/D', $key)) {
            throw new RuntimeException('Unsupported translation key for file inspection.');
        }
        [$group, $nested] = explode('.', $key, 2);
        $normalize = static fn ($locale) => strtolower(str_replace('_', '-', $locale));
        $folders = glob($root.'/*', GLOB_ONLYDIR) ?: [];
        $result = [];
        foreach (array_unique($locales) as $locale) {
            $identity = $normalize($locale);
            if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $identity)) continue;
            $matches = array_values(array_filter($folders, fn ($folder) => $normalize(basename($folder)) === $identity));
            $path = (count($matches) === 1 ? basename($matches[0]) : $identity).'/'.$group.'.php';
            $row = ['locale' => $locale, 'path' => $path, 'actual' => false, 'state' => 'unavailable', 'detail' => ''];
            $receipts = array_values(array_filter($publications, fn ($entry) => $normalize($entry['locale']) === $identity && $entry['full_name'] === $key));
            $saved = array_values(array_filter($revisions, fn ($entry) => $normalize($entry['locale']) === $identity));
            $hasFileValue = false;
            try {
                if (count($matches) > 1) throw new RuntimeException('Several directories represent this locale. Resolve the directory ambiguity first.');
                $absolute = (new SourceReplacementPreview)->path($root, $path);
                $row['actual'] = true;
                $bytes = @file_get_contents($absolute);
                if ($bytes === false) throw new RuntimeException('The language file cannot be read.');
                $tree = (new BaselineValueReader)->treeFromBytes($bytes, true);
                $value = $tree;
                foreach (explode('.', $nested) as $segment) {
                    $value = is_array($value) && array_key_exists($segment, $value) ? $value[$segment] : null;
                }
                if (@hash_file('sha256', $absolute) !== hash('sha256', $bytes)) throw new RuntimeException('The language file changed during inspection. Reopen the preview.');
                $hasFileValue = is_string($value);
                $matching = array_values(array_filter($receipts, fn ($entry) => is_string($value) && $entry['value'] === $value));
                if (! is_string($value)) {
                    $row['state'] = 'key_missing';
                    $row['detail'] = 'The file exists, but this key has no scalar translation in it.';
                } elseif ($matching !== []) {
                    $isCurrent = $saved !== [] && count(array_filter($matching, fn ($entry) => (int) $entry['revision'] === (int) $saved[0]['id'])) > 0;
                    $row['state'] = $isCurrent ? 'published' : 'published_previous';
                    $row['detail'] = $isCurrent ? 'The file value matches the publication record for the current saved revision.' : 'The file value matches a publication record, but not the current saved revision.';
                } else {
                    $row['state'] = $receipts === [] ? 'present' : 'changed';
                    $row['detail'] = $receipts === [] ? 'The key is present in the file, but there is no matching completed Workbench publication record for this destination.' : 'The file value differs from its completed publication record.';
                }
            } catch (RuntimeException $error) {
                $row['detail'] = $error->getMessage();
            }
            if ($identity === 'en' || $saved !== [] || $receipts !== [] || $hasFileValue) {
                $result[] = $row;
            }
        }
        return $result;
    }
}
