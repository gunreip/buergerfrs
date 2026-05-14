<?php

// database/migrations/2026_05_13_082530_expand_countries_for_iso_cldr_metadata.php

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
        Schema::table('countries', function (Blueprint $table) {
            $table->char('iso_numeric', 3)->nullable()->unique()->after('iso3');

            $table->string('official_name')->nullable()->after('name');
            $table->string('common_name')->nullable()->after('official_name');

            $table->string('capital')->nullable()->after('phone_code');
            $table->char('continent_code', 2)->nullable()->after('capital');
            $table->string('region')->nullable()->after('continent_code');
            $table->string('subregion')->nullable()->after('region');

            $table->decimal('latitude', 10, 7)->nullable()->after('subregion');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');

            $table->string('emoji_flag', 16)->nullable()->after('longitude');
            $table->string('tld', 16)->nullable()->after('emoji_flag');

            $table->boolean('is_independent')->nullable()->after('is_active');
            $table->boolean('is_eu_member')->default(false)->after('is_independent');
            $table->boolean('is_eea_member')->default(false)->after('is_eu_member');
            $table->boolean('is_schengen_member')->default(false)->after('is_eea_member');

            $table->boolean('postal_code_required')->nullable()->after('is_schengen_member');
            $table->string('postal_code_regex')->nullable()->after('postal_code_required');
            $table->string('address_format_key')->nullable()->after('postal_code_regex');

            $table->index('iso_numeric');
            $table->index('continent_code');
            $table->index('region');
            $table->index('subregion');
            $table->index('is_independent');
            $table->index('is_eu_member');
            $table->index('is_eea_member');
            $table->index('is_schengen_member');
            $table->index('address_format_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropIndex(['iso_numeric']);
            $table->dropIndex(['continent_code']);
            $table->dropIndex(['region']);
            $table->dropIndex(['subregion']);
            $table->dropIndex(['is_independent']);
            $table->dropIndex(['is_eu_member']);
            $table->dropIndex(['is_eea_member']);
            $table->dropIndex(['is_schengen_member']);
            $table->dropIndex(['address_format_key']);

            $table->dropUnique(['iso_numeric']);

            $table->dropColumn([
                'iso_numeric',
                'official_name',
                'common_name',
                'capital',
                'continent_code',
                'region',
                'subregion',
                'latitude',
                'longitude',
                'emoji_flag',
                'tld',
                'is_independent',
                'is_eu_member',
                'is_eea_member',
                'is_schengen_member',
                'postal_code_required',
                'postal_code_regex',
                'address_format_key',
            ]);
        });
    }
};
