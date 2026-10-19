<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('translation_workbench_findings', function (Blueprint $table) {
            // Historical rows remain unknown until a source comparison proves the position.
            $table->unsignedInteger('source_column')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('translation_workbench_findings', fn (Blueprint $table) => $table->dropColumn('source_column'));
    }
};
