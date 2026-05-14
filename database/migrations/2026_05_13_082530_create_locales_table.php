<?php

// database/migrations/2026_05_13_082530_create_locales_table.php

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
        Schema::create('locales', function (Blueprint $table) {
            $table->id();

            $table->string('code', 32)->unique();
            $table->string('normalized_code', 32)->unique();

            $table->foreignId('language_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();

            $table->char('script_code', 4)->nullable();
            $table->string('variant', 32)->nullable();

            $table->string('display_name')->nullable();
            $table->string('native_display_name')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->index('script_code');
            $table->index('variant');
            $table->index('is_active');
            $table->index('is_default');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locales');
    }
};
