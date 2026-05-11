<?php

// database/migrations/2026_05_10_023138_create_person_health_insurances_table.php

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
        Schema::create('person_health_insurances', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('person_id')
                ->constrained('people')
                ->cascadeOnDelete();

            $table->foreignId('insurance_provider_id')
                ->nullable()
                ->constrained('insurance_providers')
                ->nullOnDelete();

            $table->string('insurance_number')->nullable();

            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();

            $table->boolean('is_primary')->default(false);

            $table->timestamp('verified_at')->nullable();

            $table->foreignId('verified_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('person_id');
            $table->index('insurance_provider_id');
            $table->index('insurance_number');
            $table->index('starts_at');
            $table->index('ends_at');
            $table->index('is_primary');
            $table->index('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_health_insurances');
    }
};
