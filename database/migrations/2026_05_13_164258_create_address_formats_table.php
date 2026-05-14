<?php

// database/migrations/2026_05_13_164258_create_address_formats_table.php

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
        Schema::create('address_formats', function (Blueprint $table) {
            $table->id();

            $table->string('key', 40)->unique();
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();

            $table->char('country_code', 2);
            $table->text('format');
            $table->text('local_format')->nullable();

            $table->jsonb('required_fields')->nullable();
            $table->jsonb('uppercase_fields')->nullable();

            $table->text('postal_code_pattern')->nullable();

            $table->string('administrative_area_type', 64)->nullable();
            $table->string('locality_type', 64)->nullable();
            $table->string('dependent_locality_type', 64)->nullable();
            $table->string('postal_code_type', 64)->nullable();

            $table->string('source', 64)->default('commerceguys/addressing');

            $table->timestamps();

            $table->unique('country_code');
            $table->index('country_id');
            $table->index('administrative_area_type');
            $table->index('locality_type');
            $table->index('dependent_locality_type');
            $table->index('postal_code_type');
            $table->index('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('address_formats');
    }
};
