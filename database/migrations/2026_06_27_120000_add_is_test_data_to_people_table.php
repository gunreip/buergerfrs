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
        Schema::table('people', function (Blueprint $table): void {
            $table->boolean('is_test_data')
                ->default(false)
                ->after('person_number');

            $table->index('is_test_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('people', function (Blueprint $table): void {
            $table->dropIndex(['is_test_data']);
            $table->dropColumn('is_test_data');
        });
    }
};
