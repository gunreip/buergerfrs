<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('translation_workbench_option_discoveries', function (Blueprint $table): void {
            if (! Schema::hasColumn('translation_workbench_option_discoveries', 'suggested_key')) {
                $table->text('suggested_key')
                    ->nullable()
                    ->after('scope');

                $table->index('suggested_key', 'tw_option_discoveries_suggested_key_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('translation_workbench_option_discoveries', function (Blueprint $table): void {
            if (Schema::hasColumn('translation_workbench_option_discoveries', 'suggested_key')) {
                $table->dropIndex('tw_option_discoveries_suggested_key_index');
                $table->dropColumn('suggested_key');
            }
        });
    }
};
