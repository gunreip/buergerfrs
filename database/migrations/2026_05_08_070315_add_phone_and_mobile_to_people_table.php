<?php

// database/migrations/2026_05_08_070315_add_phone_and_mobile_to_people_table.php

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
            $table->string('phone')
                ->nullable()
                ->after('date_of_birth');

            $table->string('mobile')
                ->nullable()
                ->after('phone');

            $table->index('phone');
            $table->index('mobile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('people', function (Blueprint $table): void {
            if (Schema::hasColumn('people', 'phone')) {
                $table->dropColumn('phone');
            }

            if (Schema::hasColumn('people', 'mobile')) {
                $table->dropColumn('mobile');
            }
        });
    }
};
