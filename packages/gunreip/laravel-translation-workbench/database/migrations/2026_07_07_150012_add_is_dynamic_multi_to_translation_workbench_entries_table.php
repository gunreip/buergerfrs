<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('translation_workbench_entries', function (Blueprint $table): void {
            $table->boolean('is_dynamic_multi')
                ->default(false)
                ->after('is_ui_key')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('translation_workbench_entries', function (Blueprint $table): void {
            $table->dropColumn('is_dynamic_multi');
        });
    }
};
