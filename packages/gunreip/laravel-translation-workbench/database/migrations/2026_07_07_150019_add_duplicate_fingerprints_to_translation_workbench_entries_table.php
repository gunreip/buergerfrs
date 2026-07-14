<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('translation_workbench_entries', function (Blueprint $table): void {
            if (! Schema::hasColumn('translation_workbench_entries', 'source_fingerprint')) {
                $table->string('source_fingerprint', 64)->nullable()->after('source_signature')->index();
            }

            if (! Schema::hasColumn('translation_workbench_entries', 'expression_fingerprint')) {
                $table->string('expression_fingerprint', 64)->nullable()->after('source_fingerprint')->index();
            }

            if (! Schema::hasColumn('translation_workbench_entries', 'semantic_fingerprint')) {
                $table->string('semantic_fingerprint', 64)->nullable()->after('expression_fingerprint')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('translation_workbench_entries', function (Blueprint $table): void {
            foreach (['semantic_fingerprint', 'expression_fingerprint', 'source_fingerprint'] as $column) {
                if (Schema::hasColumn('translation_workbench_entries', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
