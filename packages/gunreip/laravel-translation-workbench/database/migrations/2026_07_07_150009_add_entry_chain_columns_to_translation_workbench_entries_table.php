<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('translation_workbench_entries', function (Blueprint $table): void {
            $table->foreignId('previous_entry_id')
                ->nullable()
                ->after('id')
                ->constrained('translation_workbench_entries')
                ->nullOnDelete();
            $table->foreignId('replaced_by_entry_id')
                ->nullable()
                ->after('previous_entry_id')
                ->constrained('translation_workbench_entries')
                ->nullOnDelete();

            $table->index(['previous_entry_id', 'replaced_by_entry_id'], 'tw_entries_chain_index');
        });
    }

    public function down(): void
    {
        Schema::table('translation_workbench_entries', function (Blueprint $table): void {
            $table->dropIndex('tw_entries_chain_index');
            $table->dropConstrainedForeignId('replaced_by_entry_id');
            $table->dropConstrainedForeignId('previous_entry_id');
        });
    }
};
