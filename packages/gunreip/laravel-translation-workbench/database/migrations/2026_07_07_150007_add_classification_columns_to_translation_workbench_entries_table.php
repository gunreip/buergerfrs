<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('translation_workbench_entries', function (Blueprint $table): void {
            $table->string('entry_type', 40)->nullable()->after('kind')->index();
            $table->string('candidate_type', 40)->nullable()->after('entry_type')->index();
            $table->string('candidate_reason', 80)->nullable()->after('candidate_type')->index();
        });
    }

    public function down(): void
    {
        Schema::table('translation_workbench_entries', function (Blueprint $table): void {
            $table->dropColumn(['entry_type', 'candidate_type', 'candidate_reason']);
        });
    }
};
