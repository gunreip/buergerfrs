<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('translation_values', function (Blueprint $table): void {
            $table->boolean('is_base_duplicate')
                ->nullable()
                ->default(null)
                ->after('value')
                ->comment('null = not checked, true = identical to base locale (redundant), false = confirmed override');
        });
    }

    public function down(): void
    {
        Schema::table('translation_values', function (Blueprint $table): void {
            $table->dropColumn('is_base_duplicate');
        });
    }
};
