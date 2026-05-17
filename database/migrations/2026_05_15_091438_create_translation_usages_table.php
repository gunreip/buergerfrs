<?php

// database/migrations/2026_05_15_091438_create_translation_usages_table.php

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
        Schema::create('translation_usages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('translation_key_id')
                ->constrained('translation_keys')
                ->cascadeOnDelete();

            $table->string('fingerprint', 64)->unique();

            $table->string('file')->index();
            $table->unsignedInteger('line')->nullable();
            $table->string('function', 64)->nullable();

            $table->string('classification', 32)->index();
            $table->string('reason')->nullable();

            $table->text('raw')->nullable();

            $table->timestamps();

            $table->index(['file', 'line']);
            $table->index(['classification', 'file']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_usages');
    }
};
