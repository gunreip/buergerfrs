<?php

// database/migrations/YYYY_MM_DD_HHMMSS_widen_country_subdivision_code_and_scope_unique_index.php

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
        Schema::table('country_subdivisions', function (Blueprint $table): void {
            $table->dropUnique('country_subdivisions_country_id_code_unique');
            $table->string('code', 255)->change();
            $table->unique(['country_id', 'parent_id', 'code'], 'country_subdivisions_country_parent_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('country_subdivisions', function (Blueprint $table): void {
            $table->dropUnique('country_subdivisions_country_parent_code_unique');
            $table->string('code', 32)->change();
            $table->unique(['country_id', 'code'], 'country_subdivisions_country_id_code_unique');
        });
    }
};
