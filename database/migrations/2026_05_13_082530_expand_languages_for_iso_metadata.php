<?php

// database/migrations/2026_05_13_082530_expand_languages_for_iso_metadata.php

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
        Schema::table('languages', function (Blueprint $table) {
            $table->char('iso639_2_b', 3)->nullable()->unique()->after('iso639_1');
            $table->char('iso639_2_t', 3)->nullable()->unique()->after('iso639_2_b');

            $table->string('scope', 32)->nullable()->after('native_name');
            $table->string('type', 32)->nullable()->after('scope');
            $table->char('macrolanguage_code', 3)->nullable()->after('type');
            $table->char('default_script', 4)->nullable()->after('macrolanguage_code');

            $table->index('scope');
            $table->index('type');
            $table->index('macrolanguage_code');
            $table->index('default_script');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('languages', function (Blueprint $table) {
            $table->dropIndex(['scope']);
            $table->dropIndex(['type']);
            $table->dropIndex(['macrolanguage_code']);
            $table->dropIndex(['default_script']);

            $table->dropUnique(['iso639_2_b']);
            $table->dropUnique(['iso639_2_t']);

            $table->dropColumn([
                'iso639_2_b',
                'iso639_2_t',
                'scope',
                'type',
                'macrolanguage_code',
                'default_script',
            ]);
        });
    }
};
