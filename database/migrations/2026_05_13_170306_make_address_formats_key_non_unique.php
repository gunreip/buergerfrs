<?php

// database/migrations/YYYY_MM_DD_HHMMSS_make_address_formats_key_non_unique.php

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
        Schema::table('address_formats', function (Blueprint $table) {
            $table->dropUnique(['key']);
            $table->index('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('address_formats', function (Blueprint $table) {
            $table->dropIndex(['key']);
            $table->unique('key');
        });
    }
};
