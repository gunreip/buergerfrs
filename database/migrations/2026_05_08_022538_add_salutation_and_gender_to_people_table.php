<?php

// database/migrations/2026_05_08_022538_add_salutation_and_gender_to_people_table.php

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
            $table->string('salutation')
                ->nullable()
                ->after('person_number');

            $table->string('gender')
                ->nullable()
                ->after('salutation');

            $table->index('salutation');
            $table->index('gender');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('people', function (Blueprint $table): void {
            $table->dropIndex(['salutation']);
            $table->dropIndex(['gender']);

            $table->dropColumn([
                'salutation',
                'gender',
            ]);
        });
    }
};
