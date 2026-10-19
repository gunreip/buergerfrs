<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('translation_workbench_findings')) {
            return;
        }
        if (! in_array(DB::getDriverName(), ['pgsql', 'sqlite'], true)) {
            throw new RuntimeException('Finding archive signature migration requires PostgreSQL or SQLite.');
        }
        Schema::table('translation_workbench_findings', function (Blueprint $table): void {
            $table->dropUnique('translation_workbench_findings_source_signature_unique');
            $table->index('source_signature', 'tw_findings_signature_history_index');
        });
        DB::statement("CREATE UNIQUE INDEX tw_findings_current_signature_unique ON translation_workbench_findings (source_signature) WHERE status <> 'obsolete'");
    }

    public function down(): void
    {
        if (! Schema::hasTable('translation_workbench_findings')) {
            return;
        }
        if (DB::table('translation_workbench_findings')->select('source_signature')
            ->groupBy('source_signature')->havingRaw('COUNT(*) > 1')->exists()) {
            throw new RuntimeException('Cannot restore global source-signature uniqueness while archived occurrences share a signature. History must not be deleted for rollback.');
        }
        Schema::table('translation_workbench_findings', function (Blueprint $table): void {
            $table->unique('source_signature', 'translation_workbench_findings_source_signature_unique');
        });
        DB::statement('DROP INDEX tw_findings_current_signature_unique');
        Schema::table('translation_workbench_findings', function (Blueprint $table): void {
            $table->dropIndex('tw_findings_signature_history_index');
        });
    }
};
