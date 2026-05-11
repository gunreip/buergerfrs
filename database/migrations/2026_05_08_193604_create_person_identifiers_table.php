<?php

// database/migrations/2026_05_08_193604_create_person_identifiers_table.php

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
        Schema::create('person_identifiers', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('person_id')
                ->constrained('people')
                ->cascadeOnDelete();

            $table->foreignId('issuing_country_id')
                ->nullable()
                ->constrained('countries')
                ->nullOnDelete();

            $table->string('type');
            $table->string('value');
            $table->char('value_hash', 64)->nullable();

            $table->string('issuing_authority')->nullable();

            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();

            $table->boolean('is_primary')->default(false);

            $table->timestamp('verified_at')->nullable();

            $table->foreignId('verified_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['person_id', 'type', 'value_hash']);

            $table->index('person_id');
            $table->index('issuing_country_id');
            $table->index('type');
            $table->index('value_hash');
            $table->index('is_primary');
            $table->index('issued_at');
            $table->index('expires_at');
            $table->index('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_identifiers');
    }
};
