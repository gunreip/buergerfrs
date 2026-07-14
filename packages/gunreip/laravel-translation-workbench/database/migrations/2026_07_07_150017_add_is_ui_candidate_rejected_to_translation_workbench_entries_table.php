<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('translation_workbench_entries', function (Blueprint $table): void {
            if (! Schema::hasColumn('translation_workbench_entries', 'is_ui_candidate_rejected')) {
                $table->boolean('is_ui_candidate_rejected')
                    ->default(false)
                    ->after('is_ui_key')
                    ->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('translation_workbench_entries', function (Blueprint $table): void {
            if (Schema::hasColumn('translation_workbench_entries', 'is_ui_candidate_rejected')) {
                $table->dropColumn('is_ui_candidate_rejected');
            }
        });
    }
};
