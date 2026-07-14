<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('translation_workbench_entries', function (Blueprint $table): void {
            $table->text('literal_text_suggested')->nullable()->after('literal_text');
        });
    }

    public function down(): void
    {
        Schema::table('translation_workbench_entries', function (Blueprint $table): void {
            $table->dropColumn('literal_text_suggested');
        });
    }
};
