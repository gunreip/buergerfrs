<?php

// database/migrations/2026_05_06_065036_create_people_table.php

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
        Schema::create('people', function (Blueprint $table): void {
            $table->id();

            $table->string('person_number')
                ->nullable()
                ->unique();

            $table->string('first_name');
            $table->string('last_name');

            $table->date('date_of_birth')
                ->nullable();

            $table->timestamps();

            $table->index('last_name');
            $table->index('first_name');
            $table->index('date_of_birth');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
