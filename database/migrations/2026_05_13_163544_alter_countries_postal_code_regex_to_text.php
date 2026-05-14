<?php

// database/migrations/YYYY_MM_DD_HHMMSS_alter_countries_postal_code_regex_to_text.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE countries ALTER COLUMN postal_code_regex TYPE text');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE countries ALTER COLUMN postal_code_regex TYPE varchar(255)');
    }
};
