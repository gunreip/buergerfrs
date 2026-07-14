<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('translation_workbench_lang_values', function (Blueprint $table): void {
            $table->boolean('is_source_locale')->default(false)->index()->after('locale');
            $table->string('locale_role', 40)->default('target_main')->index()->after('is_source_locale');
            $table->string('main_locale', 20)->nullable()->index()->after('locale_role');
            $table->string('parent_locale', 20)->nullable()->index()->after('main_locale');
        });

        DB::table('translation_workbench_lang_values')
            ->orderBy('id')
            ->chunkById(500, function ($rows): void {
                foreach ($rows as $row) {
                    $locale = (string) $row->locale;
                    $mainLocale = explode('-', $locale, 2)[0] ?: $locale;
                    $isSourceLocale = $locale === 'en';
                    $isSubLocale = str_contains($locale, '-');

                    DB::table('translation_workbench_lang_values')
                        ->where('id', $row->id)
                        ->update([
                            'is_source_locale' => $isSourceLocale,
                            'locale_role' => $isSourceLocale
                                ? 'source_main'
                                : ($isSubLocale ? 'target_sub' : 'target_main'),
                            'main_locale' => $isSourceLocale ? 'en' : $mainLocale,
                            'parent_locale' => $isSubLocale ? $mainLocale : null,
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('translation_workbench_lang_values', function (Blueprint $table): void {
            $table->dropColumn([
                'is_source_locale',
                'locale_role',
                'main_locale',
                'parent_locale',
            ]);
        });
    }
};
