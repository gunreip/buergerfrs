<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('translation_workbench_keys', function (Blueprint $table): void {
            if (! Schema::hasColumn('translation_workbench_keys', 'lang_node_type')) {
                $table->string('lang_node_type', 40)->default('unknown')->index()->after('key_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('translation_workbench_keys', function (Blueprint $table): void {
            if (Schema::hasColumn('translation_workbench_keys', 'lang_node_type')) {
                $table->dropColumn('lang_node_type');
            }
        });
    }
};
