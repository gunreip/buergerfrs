<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('translation_workbench_entries', function (Blueprint $table): void {
            if (! Schema::hasColumn('translation_workbench_entries', 'deleted_segments')) {
                $table->json('deleted_segments')->nullable()->after('translation_key_source');
            }
        });
    }

    public function down(): void
    {
        Schema::table('translation_workbench_entries', function (Blueprint $table): void {
            if (Schema::hasColumn('translation_workbench_entries', 'deleted_segments')) {
                $table->dropColumn('deleted_segments');
            }
        });
    }
};
