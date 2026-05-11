<?php

// database/migrations/2026_05_08_025033_add_middle_name_and_preferred_name_to_people_table.php

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
            $table->string('middle_name')
                ->nullable()
                ->after('first_name');

            $table->string('preferred_name')
                ->nullable()
                ->after('middle_name');

            $table->index('middle_name');
            $table->index('preferred_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('people', function (Blueprint $table): void {
            if (Schema::hasColumn('people', 'middle_name')) {
                $table->dropColumn('middle_name');
            }

            if (Schema::hasColumn('people', 'preferred_name')) {
                $table->dropColumn('preferred_name');
            }
        });
    }
};
