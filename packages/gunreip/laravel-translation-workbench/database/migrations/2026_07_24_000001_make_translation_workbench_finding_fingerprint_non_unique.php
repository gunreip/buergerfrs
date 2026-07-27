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

        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE translation_workbench_findings DROP CONSTRAINT IF EXISTS translation_workbench_findings_fingerprint_unique');
            DB::statement('CREATE INDEX IF NOT EXISTS translation_workbench_findings_fingerprint_index ON translation_workbench_findings (fingerprint)');

            return;
        }

        Schema::table('translation_workbench_findings', function (Blueprint $table): void {
            $table->dropUnique('translation_workbench_findings_fingerprint_unique');
            $table->index('fingerprint', 'translation_workbench_findings_fingerprint_index');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('translation_workbench_findings')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS translation_workbench_findings_fingerprint_index');
            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS translation_workbench_findings_fingerprint_unique ON translation_workbench_findings (fingerprint)');

            return;
        }

        Schema::table('translation_workbench_findings', function (Blueprint $table): void {
            $table->dropIndex('translation_workbench_findings_fingerprint_index');
            $table->unique('fingerprint', 'translation_workbench_findings_fingerprint_unique');
        });
    }
};
