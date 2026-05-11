<?php

// database/migrations/2026_05_10_023138_create_insurance_providers_table.php

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
        Schema::create('insurance_providers', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('country_id')
                ->nullable()
                ->constrained('countries')
                ->nullOnDelete();

            $table->string('type')->default('health');
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('code')->nullable();

            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index('country_id');
            $table->index('type');
            $table->index('name');
            $table->index('short_name');
            $table->index('code');
            $table->index('is_active');
            $table->index('sort_order');

            $table->unique(['country_id', 'type', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_providers');
    }
};
