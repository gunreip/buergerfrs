<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_workbench_timeline_chains', function (Blueprint $table): void {
            $table->id();
            $table->string('chain_key')->unique();
            $table->text('translation_key')->nullable();
            $table->text('normalized_translation_key')->nullable()->index();
            $table->string('namespace')->nullable()->index();
            $table->string('group')->nullable()->index();
            $table->string('chain_type', 40)->default('single')->index();
            $table->string('chain_status', 40)->default('active')->index();
            $table->foreignId('root_key_id')
                ->nullable()
                ->constrained('translation_workbench_keys')
                ->nullOnDelete();
            $table->foreignId('root_finding_id')
                ->nullable()
                ->constrained('translation_workbench_findings')
                ->nullOnDelete();
            $table->unsignedInteger('key_count')->default(0);
            $table->unsignedInteger('finding_count')->default(0);
            $table->unsignedInteger('active_finding_count')->default(0);
            $table->unsignedInteger('obsolete_finding_count')->default(0);
            $table->unsignedInteger('commented_out_finding_count')->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->unsignedInteger('timeline_event_count')->default(0);
            $table->unsignedInteger('lang_value_count')->default(0);
            $table->unsignedInteger('shared_candidate_count')->default(0);
            $table->unsignedInteger('bulk_review_count')->default(0);
            $table->json('key_ids')->nullable();
            $table->json('finding_ids')->nullable();
            $table->json('review_ids')->nullable();
            $table->json('timeline_event_ids')->nullable();
            $table->json('lang_value_ids')->nullable();
            $table->json('related_translation_keys')->nullable();
            $table->json('relation_summary')->nullable();
            $table->json('lang_value_summary')->nullable();
            $table->json('timeline_event_summary')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('first_seen_at')->nullable()->index();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->unsignedInteger('scan_count')->default(0);
            $table->timestamps();

            $table->index(['namespace', 'group', 'chain_status'], 'tw_timeline_chains_namespace_group_status');
            $table->index(['chain_type', 'chain_status'], 'tw_timeline_chains_type_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_workbench_timeline_chains');
    }
};
