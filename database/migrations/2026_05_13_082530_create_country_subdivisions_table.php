<?php

// database/migrations/2026_05_13_082530_create_country_subdivisions_table.php

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
        Schema::create('country_subdivisions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('country_subdivisions')->nullOnDelete();

            $table->string('code', 32);
            $table->string('iso_code', 64)->nullable();

            $table->string('type', 64)->nullable();
            $table->string('name');
            $table->string('local_name')->nullable();

            $table->string('postal_code_pattern')->nullable();

            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->unique(['country_id', 'code']);
            $table->index('parent_id');
            $table->index('iso_code');
            $table->index('type');
            $table->index('name');
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_subdivisions');
    }
};
