<?php

use Gunreip\TranslationWorkbench\Support\SourcePathSegmentFactory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('translation_workbench_source_files', function (Blueprint $table): void {
            $table->string('source_root')->nullable()->index()->after('path');
            $table->string('source_area')->nullable()->index()->after('source_root');
            $table->string('package_vendor')->nullable()->index()->after('source_area');
            $table->string('package_name')->nullable()->index()->after('package_vendor');
            $table->string('path_domain')->nullable()->index()->after('package_name');
            $table->string('path_section')->nullable()->index()->after('path_domain');
            $table->string('path_context')->nullable()->index()->after('path_section');
            $table->string('path_scope')->nullable()->index()->after('path_context');
            $table->string('path_extra')->nullable()->index()->after('path_scope');
            $table->string('filename')->nullable()->index()->after('path_extra');

            $table->index([
                'source_root',
                'source_area',
                'path_domain',
                'path_section',
                'path_context',
            ], 'tw_source_files_path_segment_index');
        });

        $segmentFactory = app(SourcePathSegmentFactory::class);

        DB::table('translation_workbench_source_files')
            ->select(['id', 'path'])
            ->orderBy('id')
            ->chunkById(500, function ($rows) use ($segmentFactory): void {
                foreach ($rows as $row) {
                    DB::table('translation_workbench_source_files')
                        ->where('id', $row->id)
                        ->update($segmentFactory->fromPath($row->path));
                }
            });
    }

    public function down(): void
    {
        Schema::table('translation_workbench_source_files', function (Blueprint $table): void {
            $table->dropIndex('tw_source_files_path_segment_index');
            $table->dropColumn([
                'source_root',
                'source_area',
                'package_vendor',
                'package_name',
                'path_domain',
                'path_section',
                'path_context',
                'path_scope',
                'path_extra',
                'filename',
            ]);
        });
    }
};
