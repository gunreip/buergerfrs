<?php

// database/migrations/2026_05_08_132426_create_person_languages_table.php

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
        Schema::create('person_languages', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('person_id')
                ->constrained('people')
                ->cascadeOnDelete();

            $table->foreignId('language_id')
                ->constrained('languages')
                ->restrictOnDelete();

            $table->string('proficiency')->default('unknown');

            $table->boolean('is_native')->default(false);
            $table->boolean('is_primary')->default(false);
            $table->boolean('preferred_for_communication')->default(false);

            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();

            $table->timestamp('verified_at')->nullable();

            $table->foreignId('verified_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['person_id', 'language_id']);

            $table->index('person_id');
            $table->index('language_id');
            $table->index('proficiency');
            $table->index('is_native');
            $table->index('is_primary');
            $table->index('preferred_for_communication');
            $table->index('starts_at');
            $table->index('ends_at');
            $table->index('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_languages');
    }
};
