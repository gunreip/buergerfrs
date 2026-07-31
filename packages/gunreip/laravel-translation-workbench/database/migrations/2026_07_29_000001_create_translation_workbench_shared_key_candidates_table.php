<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_workbench_shared_key_candidates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('finding_id')
                ->constrained('translation_workbench_findings')
                ->cascadeOnDelete();
            $table->foreignId('key_id')
                ->nullable()
                ->constrained('translation_workbench_keys')
                ->nullOnDelete();
            $table->foreignId('matched_key_id')
                ->nullable()
                ->constrained('translation_workbench_keys')
                ->nullOnDelete();
            $table->string('normalized_literal')->index();
            $table->longText('literal_text')->nullable();
            $table->text('current_translation_key')->nullable();
            $table->text('suggested_shared_translation_key');
            $table->unsignedInteger('matched_review_count')->default(0);
            $table->unsignedInteger('matched_finding_count')->default(0);
            $table->string('confidence', 40)->default('medium')->index();
            $table->string('status', 40)->default('pending')->index();
            $table->timestamp('first_seen_at')->nullable()->index();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->json('matched_finding_ids')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['finding_id', 'suggested_shared_translation_key'], 'tw_shared_key_candidates_unique');
            $table->index(['normalized_literal', 'status'], 'tw_shared_key_candidates_literal_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_workbench_shared_key_candidates');
    }
};
