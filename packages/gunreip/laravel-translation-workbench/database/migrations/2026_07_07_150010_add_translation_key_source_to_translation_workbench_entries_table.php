<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('translation_workbench_entries', function (Blueprint $table): void {
            $table->string('translation_key_source', 40)
                ->nullable()
                ->after('translation_key')
                ->index();
        });

        DB::table('translation_workbench_entries')
            ->whereNotNull('translation_key')
            ->whereColumn('translation_key', 'existing_key')
            ->update(['translation_key_source' => 'code']);

        DB::table('translation_workbench_entries')
            ->whereNotNull('translation_key')
            ->whereColumn('translation_key', 'suggested_key')
            ->whereIn('review_status', ['reviewed', 'approved'])
            ->update(['translation_key_source' => 'suggested']);

        DB::table('translation_workbench_entries')
            ->whereNotNull('translation_key')
            ->whereExists(function ($query): void {
                $query
                    ->selectRaw('1')
                    ->from('translation_workbench_events')
                    ->whereColumn('translation_workbench_events.entry_id', 'translation_workbench_entries.id')
                    ->where('translation_workbench_events.event_type', 'translation_key_updated');
            })
            ->update(['translation_key_source' => 'manual']);
    }

    public function down(): void
    {
        Schema::table('translation_workbench_entries', function (Blueprint $table): void {
            $table->dropColumn('translation_key_source');
        });
    }
};
