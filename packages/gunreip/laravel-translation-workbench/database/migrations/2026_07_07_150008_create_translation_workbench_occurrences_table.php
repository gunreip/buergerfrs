<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_workbench_occurrences', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('entry_id')
                ->constrained('translation_workbench_entries')
                ->cascadeOnDelete();
            $table->string('source_signature', 64)->unique();
            $table->string('source_type', 40)->index();
            $table->string('source_path')->index();
            $table->unsignedInteger('source_line')->nullable();
            $table->string('function_name', 80)->nullable()->index();
            $table->text('raw_expression')->nullable();
            $table->text('suggested_key')->nullable();
            $table->text('literal_text_suggested')->nullable();
            $table->string('status', 40)->default('active')->index();
            $table->timestamp('first_seen_at')->nullable()->index();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->unsignedInteger('scan_count')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['entry_id', 'status'], 'tw_occurrences_entry_status_index');
            $table->index(['source_path', 'source_line'], 'tw_occurrences_source_position_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_workbench_occurrences');
    }
};
