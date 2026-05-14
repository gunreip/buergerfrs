<?php

// database/migrations/YYYY_MM_DD_HHMMSS_drop_redundant_iso_numeric_index_from_countries_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropIndex(['iso_numeric']);
        });
    }

    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->index('iso_numeric');
        });
    }
};
