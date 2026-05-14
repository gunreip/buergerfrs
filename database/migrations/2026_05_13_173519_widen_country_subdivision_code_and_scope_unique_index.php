<?php

// database/migrations/YYYY_MM_DD_HHMMSS_widen_country_subdivision_code_and_scope_unique_index.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE country_subdivisions DROP CONSTRAINT country_subdivisions_country_id_code_unique');
        DB::statement('ALTER TABLE country_subdivisions ALTER COLUMN code TYPE varchar(255)');
        DB::statement('CREATE UNIQUE INDEX country_subdivisions_country_parent_code_unique ON country_subdivisions (country_id, parent_id, code)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX country_subdivisions_country_parent_code_unique');
        DB::statement('ALTER TABLE country_subdivisions ALTER COLUMN code TYPE varchar(32)');
        DB::statement('ALTER TABLE country_subdivisions ADD CONSTRAINT country_subdivisions_country_id_code_unique UNIQUE (country_id, code)');
    }
};
