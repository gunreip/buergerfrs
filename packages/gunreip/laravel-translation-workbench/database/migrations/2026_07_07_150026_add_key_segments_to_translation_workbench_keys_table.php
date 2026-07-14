<?php

use Gunreip\TranslationWorkbench\Support\TranslationKeySegmentFactory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('translation_workbench_keys', function (Blueprint $table): void {
            $table->string('key_segment_domain')->nullable()->after('scope')->index();
            $table->string('key_segment_section')->nullable()->after('key_segment_domain')->index();
            $table->string('key_segment_context')->nullable()->after('key_segment_section')->index();
            $table->string('key_segment_extra')->nullable()->after('key_segment_context')->index();
            $table->string('key_segment_name')->nullable()->after('key_segment_extra')->index();

            $table->index([
                'namespace',
                'group',
                'key_segment_domain',
                'key_segment_section',
                'key_segment_context',
            ], 'tw_keys_segment_filter_index');
        });

        $segmentFactory = app(TranslationKeySegmentFactory::class);

        DB::table('translation_workbench_keys')
            ->select(['id', 'suggested_key'])
            ->orderBy('id')
            ->chunkById(500, function ($rows) use ($segmentFactory): void {
                foreach ($rows as $row) {
                    DB::table('translation_workbench_keys')
                        ->where('id', $row->id)
                        ->update($segmentFactory->fromKey($row->suggested_key));
                }
            });
    }

    public function down(): void
    {
        Schema::table('translation_workbench_keys', function (Blueprint $table): void {
            $table->dropIndex('tw_keys_segment_filter_index');
            $table->dropColumn([
                'key_segment_domain',
                'key_segment_section',
                'key_segment_context',
                'key_segment_extra',
                'key_segment_name',
            ]);
        });
    }
};
