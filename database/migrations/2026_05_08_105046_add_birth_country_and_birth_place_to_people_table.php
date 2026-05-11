<?php

// php artisan make:migration add_birth_country_and_birth_place_to_people_table --table=people

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
        Schema::table('people', function (Blueprint $table): void {
            $table->foreignId('birth_country_id')
                ->nullable()
                ->after('date_of_birth')
                ->constrained('countries')
                ->nullOnDelete();

            $table->string('birth_place_text')
                ->nullable()
                ->after('birth_country_id');

            $table->index('birth_place_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('people', function (Blueprint $table): void {
            if (Schema::hasColumn('people', 'birth_country_id')) {
                $table->dropConstrainedForeignId('birth_country_id');
            }

            if (Schema::hasColumn('people', 'birth_place_text')) {
                $table->dropColumn('birth_place_text');
            }
        });
    }
};
