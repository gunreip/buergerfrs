<?php

// database/migrations/2026_05_15_091438_create_translation_values_table.php

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
        Schema::create('translation_values', function (Blueprint $table) {
            $table->id();

            $table->foreignId('translation_key_id')
                ->constrained('translation_keys')
                ->cascadeOnDelete();

            $table->string('locale', 20)->index();

            $table->longText('value')->nullable();

            $table->string('status', 32)->default('missing')->index();
            $table->string('source', 32)->default('audit')->index();

            $table->timestamp('reviewed_at')->nullable();

            $table->foreignId('reviewed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(['translation_key_id', 'locale']);
            $table->index(['locale', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_values');
    }
};
