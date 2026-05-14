<?php

// database/migrations/2026_05_13_082530_create_country_names_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('country_names', function (Blueprint $table) {
            $table->id();

            $table->foreignId('country_id')->constrained()->cascadeOnDelete();

            $table->string('locale', 16);
            $table->string('name');
            $table->string('official_name')->nullable();
            $table->string('common_name')->nullable();

            $table->string('source', 64)->nullable();
            $table->boolean('is_default')->default(false);

            $table->timestamps();

            $table->unique(['country_id', 'locale']);
            $table->index('locale');
            $table->index('name');
            $table->index('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_names');
    }
};
