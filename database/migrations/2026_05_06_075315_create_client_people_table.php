<?php

// database/migrations/2026_05_06_075315_create_client_people_table.php

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
        Schema::create('client_person', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();

            $table->foreignId('person_id')
                ->constrained('people')
                ->cascadeOnDelete();

            $table->string('relationship_type')
                ->default('member');

            $table->string('status')
                ->default('pending');

            $table->boolean('is_primary')
                ->default(false);

            $table->date('starts_at')
                ->nullable();

            $table->date('ends_at')
                ->nullable();

            $table->timestamp('verified_at')
                ->nullable();

            $table->foreignId('verified_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('created_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->unique(['client_id', 'person_id', 'relationship_type']);

            $table->index('relationship_type');
            $table->index('status');
            $table->index('is_primary');
            $table->index('starts_at');
            $table->index('ends_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_person');
    }
};
