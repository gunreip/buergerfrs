<?php

// database/migrations/2026_05_22_030139_create_translation_audit_events_table.php

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
        Schema::create('translation_audit_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('translation_key_id')
                ->nullable()
                ->constrained('translation_keys')
                ->nullOnDelete();

            $table->foreignId('translation_usage_id')
                ->nullable()
                ->constrained('translation_usages')
                ->nullOnDelete();

            $table->string('entity_type', 50);
            $table->string('event_type', 80);

            $table->string('old_fingerprint')->nullable();
            $table->string('new_fingerprint')->nullable();

            $table->string('old_file')->nullable();
            $table->string('new_file')->nullable();

            $table->unsignedInteger('old_line')->nullable();
            $table->unsignedInteger('new_line')->nullable();

            $table->string('old_key')->nullable();
            $table->string('new_key')->nullable();

            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();

            $table->string('old_status', 50)->nullable();
            $table->string('new_status', 50)->nullable();

            $table->string('reason')->nullable();
            $table->json('context')->nullable();

            $table->timestamps();

            $table->index(['entity_type', 'event_type']);
            $table->index(['translation_key_id', 'entity_type', 'event_type'], 'translation_audit_events_key_entity_event_index');
            $table->index(['translation_usage_id', 'entity_type', 'event_type'], 'translation_audit_events_usage_entity_event_index');
            $table->index(['created_at', 'event_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_audit_events');
    }
};
