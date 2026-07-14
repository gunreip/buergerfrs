<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_workbench_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('entry_id')
                ->nullable()
                ->constrained('translation_workbench_entries')
                ->nullOnDelete();
            $table->string('event_type', 80)->index();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('context')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['entry_id', 'event_type'], 'tw_events_entry_event_index');
            $table->index(['created_at', 'event_type'], 'tw_events_created_event_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_workbench_events');
    }
};
