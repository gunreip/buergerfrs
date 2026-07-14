<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('translation_usages', function (Blueprint $table): void {
            $table->index(
                'translation_key_id',
                'translation_usages_translation_key_id_index',
            );
        });
    }

    public function down(): void
    {
        Schema::table('translation_usages', function (Blueprint $table): void {
            $table->dropIndex('translation_usages_translation_key_id_index');
        });
    }
};
