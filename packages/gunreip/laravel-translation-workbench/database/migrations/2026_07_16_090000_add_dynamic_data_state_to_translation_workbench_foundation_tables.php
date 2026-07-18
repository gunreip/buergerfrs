<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('translation_workbench_findings', function (Blueprint $table): void {
            if (! Schema::hasColumn('translation_workbench_findings', 'dynamic_data_state')) {
                $table->string('dynamic_data_state', 40)
                    ->nullable()
                    ->after('dynamic_scope')
                    ->index();
            }
        });

        Schema::table('translation_workbench_keys', function (Blueprint $table): void {
            if (! Schema::hasColumn('translation_workbench_keys', 'dynamic_data_state')) {
                $table->string('dynamic_data_state', 40)
                    ->nullable()
                    ->after('is_dynamic_multi')
                    ->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('translation_workbench_keys', function (Blueprint $table): void {
            if (Schema::hasColumn('translation_workbench_keys', 'dynamic_data_state')) {
                $table->dropColumn('dynamic_data_state');
            }
        });

        Schema::table('translation_workbench_findings', function (Blueprint $table): void {
            if (Schema::hasColumn('translation_workbench_findings', 'dynamic_data_state')) {
                $table->dropColumn('dynamic_data_state');
            }
        });
    }
};
