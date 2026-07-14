<?php

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
        Schema::create('address_postal_codes', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnDelete();

            $table->string('postal_code');
            $table->string('normalized_postal_code');
            $table->boolean('is_verified')->default(false);
            $table->string('source', 64)->default('manual');
            $table->timestamps();

            $table->unique(['country_id', 'normalized_postal_code'], 'address_postal_codes_country_normalized_unique');
            $table->index(['country_id', 'postal_code']);
            $table->index('is_verified');
            $table->index('source');
        });

        Schema::create('address_localities', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnDelete();

            $table->foreignId('postal_code_id')
                ->nullable()
                ->constrained('address_postal_codes')
                ->nullOnDelete();

            $table->string('name');
            $table->string('normalized_name');
            $table->boolean('is_verified')->default(false);
            $table->string('source', 64)->default('manual');
            $table->timestamps();

            $table->unique(['country_id', 'postal_code_id', 'normalized_name'], 'address_localities_scope_normalized_unique');
            $table->index(['country_id', 'name']);
            $table->index('postal_code_id');
            $table->index('is_verified');
            $table->index('source');
        });

        Schema::create('address_streets', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnDelete();

            $table->foreignId('postal_code_id')
                ->nullable()
                ->constrained('address_postal_codes')
                ->nullOnDelete();

            $table->foreignId('locality_id')
                ->nullable()
                ->constrained('address_localities')
                ->nullOnDelete();

            $table->string('name');
            $table->string('normalized_name');
            $table->boolean('is_verified')->default(false);
            $table->string('source', 64)->default('manual');
            $table->timestamps();

            $table->unique(['country_id', 'postal_code_id', 'locality_id', 'normalized_name'], 'address_streets_scope_normalized_unique');
            $table->index(['country_id', 'name']);
            $table->index('postal_code_id');
            $table->index('locality_id');
            $table->index('is_verified');
            $table->index('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('address_streets');
        Schema::dropIfExists('address_localities');
        Schema::dropIfExists('address_postal_codes');
    }
};
