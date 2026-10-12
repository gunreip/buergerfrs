<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;

use App\Support\Locale\LocaleCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class LangValueLabels
{
    /**
     * Return only the graph-relevant lang values: source locale plus current UI
     * target main locale. Context references retain the actual language-value ID.
     *
     * @param  array<string, mixed>  $mainRow
     * @return array<string, array<mixed>>
     */
    public static function active(array $mainRow, bool $withContext = false): array
    {
        $langValueIds = ValueNormalizer::integerList($mainRow['lang_value_ids'] ?? []);

        if ($langValueIds === [] || ! Schema::hasTable('translation_workbench_lang_values')) {
            return [];
        }

        $sourceLocale = LocaleCode::normalize((string) config('translation-workbench.source_locale', 'en')) ?: 'en';
        $targetLocale = LocaleResolver::activeTargetMainLocale();
        $rows = DB::table('translation_workbench_lang_values')
            ->whereIn('id', $langValueIds)
            ->where('status', 'active')
            ->whereIn('locale', array_values(array_unique(array_filter([$sourceLocale, $targetLocale]))))
            ->get(['id', 'locale', 'locale_role', 'value', 'last_seen_at', 'updated_at', 'created_at']);

        $source = $rows->first(static function (object $row) use ($sourceLocale): bool {
            return (string) $row->locale === $sourceLocale
                || (string) $row->locale_role === 'source_main';
        });
        $target = $rows->first(static function (object $row) use ($targetLocale, $sourceLocale): bool {
            return (string) $row->locale === $targetLocale
                && (string) $row->locale !== $sourceLocale;
        }) ?? $rows->first(static fn(object $row): bool => (string) $row->locale_role === 'target_main');
        $labels = [];

        if ($source !== null) {
            $labels['left'] = [
                'source lang value ID #' . (string) $source->id,
                LabelFormatter::langValueTimestampLine($source),
            ];
        }

        if ($target !== null) {
            $labels['right'] = [
                'target lang value ID #' . (string) $target->id,
                LabelFormatter::langValueTimestampLine($target),
            ];
        }

        if ($withContext) {
            foreach (['left' => $source, 'right' => $target] as $side => $row) {
                if ($row !== null && isset($labels[$side])) {
                    $labels[$side] = [
                        'text' => $labels[$side],
                        'contextMenu' => \Gunreip\TranslationWorkbench\Support\DatasetLabelContext::menu(['type' => 'lang-value', 'id' => $row->id]),
                    ];
                }
            }
        }
        return $labels;
    }
}
