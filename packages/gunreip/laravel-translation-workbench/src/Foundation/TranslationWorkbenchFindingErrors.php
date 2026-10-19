<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/** Read-only filter for errors displayed beside the selected finding's key. */
class TranslationWorkbenchFindingErrors
{
    public static function apply(Builder $query): void
    {
        // Ignore the optional package namespace when counting the file/key levels.
        $key = DB::getDriverName() === 'pgsql'
            ? "CASE WHEN POSITION('::' IN keys.translation_key) > 0 THEN SPLIT_PART(keys.translation_key, '::', 2) ELSE keys.translation_key END"
            : "CASE WHEN INSTR(keys.translation_key, '::') > 0 THEN SUBSTR(keys.translation_key, INSTR(keys.translation_key, '::') + 2) ELSE keys.translation_key END";
        $query->where(function (Builder $errors) use ($key): void {
            $errors->where(function (Builder $scalar) use ($key): void {
                $scalar->whereRaw('COALESCE(keys.is_dynamic_multi, false) = false')
                    ->whereRaw("COALESCE(keys.lang_node_type, '') <> 'container'")
                    ->whereRaw("($key) LIKE ?", ['%.%'])
                    ->whereRaw("($key) NOT LIKE ?", ['%.%.%'])
                    ->whereRaw("($key) NOT LIKE ?", ['.%'])
                    ->whereRaw("($key) NOT LIKE ?", ['%.']);
            })->orWhereIn('keys.translation_key', TranslationWorkbenchKeyOwnership::duplicateKeys());
        });
    }
}
