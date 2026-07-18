<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('translation_workbench_keys', function (Blueprint $table): void {
            if (! Schema::hasColumn('translation_workbench_keys', 'reviewed_is_ui_candidate')) {
                $table->boolean('reviewed_is_ui_candidate')
                    ->nullable()
                    ->after('is_ui_candidate_rejected')
                    ->index();
            }

            if (! Schema::hasColumn('translation_workbench_keys', 'reviewed_is_dynamic_candidate')) {
                $table->boolean('reviewed_is_dynamic_candidate')
                    ->nullable()
                    ->after('is_dynamic_candidate_rejected')
                    ->index();
            }

            if (! Schema::hasColumn('translation_workbench_keys', 'reviewed_is_dynamic_multi')) {
                $table->boolean('reviewed_is_dynamic_multi')
                    ->nullable()
                    ->after('is_dynamic_multi')
                    ->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('translation_workbench_keys', function (Blueprint $table): void {
            if (Schema::hasColumn('translation_workbench_keys', 'reviewed_is_dynamic_multi')) {
                $table->dropColumn('reviewed_is_dynamic_multi');
            }

            if (Schema::hasColumn('translation_workbench_keys', 'reviewed_is_dynamic_candidate')) {
                $table->dropColumn('reviewed_is_dynamic_candidate');
            }

            if (Schema::hasColumn('translation_workbench_keys', 'reviewed_is_ui_candidate')) {
                $table->dropColumn('reviewed_is_ui_candidate');
            }
        });
    }
};
