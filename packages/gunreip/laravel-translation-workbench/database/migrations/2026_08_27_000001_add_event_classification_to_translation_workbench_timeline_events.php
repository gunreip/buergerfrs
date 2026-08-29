<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('translation_workbench_timeline_events', function (Blueprint $table): void {
            $table->string('event_classification', 80)
                ->default('normal')
                ->after('event_type')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('translation_workbench_timeline_events', function (Blueprint $table): void {
            $table->dropColumn('event_classification');
        });
    }
};
