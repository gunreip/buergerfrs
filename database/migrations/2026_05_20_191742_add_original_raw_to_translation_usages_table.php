<?php

// database/migrations/2026_05_20_191742_add_original_raw_to_translation_usages_table.php

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
        Schema::table('translation_usages', function (Blueprint $table) {
            $table
                ->text('original_raw')
                ->nullable()
                ->after('raw');

            // TODO: Optional explicit backfill for existing rows can set original_raw = raw once,
            // but this must stay a conscious data maintenance step and must not be repeated by sync commands.
            // der Backfill:
            // php artisan tinker --execute="\DB::table('translation_usages')->whereNull('original_raw')->update(['original_raw' => \DB::raw('raw')]);"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('translation_usages', function (Blueprint $table) {
            $table->dropColumn('original_raw');
        });
    }
};
