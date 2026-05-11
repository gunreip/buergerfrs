<?php

// database/migrations/2026_05_10_041517_add_birth_name_to_people_table.php

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
            $table->string('birth_name')->nullable()->after('last_name');

            $table->index('birth_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('people', function (Blueprint $table): void {
            $table->dropIndex(['birth_name']);

            $table->dropColumn('birth_name');
        });
    }
};
