<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_workbench_source_files', function (Blueprint $table): void {
            $table->id();
            $table->string('path')->unique();
            $table->string('source_root')->nullable()->index();
            $table->string('source_area')->nullable()->index();
            $table->string('package_vendor')->nullable()->index();
            $table->string('package_name')->nullable()->index();
            $table->string('path_domain')->nullable()->index();
            $table->string('path_section')->nullable()->index();
            $table->string('path_context')->nullable()->index();
            $table->string('path_scope')->nullable()->index();
            $table->string('path_extra')->nullable()->index();
            $table->string('filename')->nullable()->index();
            $table->string('source_type', 40)->index();
            $table->string('extension', 20)->nullable()->index();
            $table->string('status', 40)->default('active')->index();
            $table->timestamp('first_seen_at')->nullable()->index();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->unsignedInteger('scan_count')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index([
                'source_root',
                'source_area',
                'path_domain',
                'path_section',
                'path_context',
            ], 'tw_source_files_path_segment_index');
        });

        Schema::create('translation_workbench_findings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('source_file_id')
                ->constrained('translation_workbench_source_files')
                ->cascadeOnDelete();
            $table->string('fingerprint', 64)->unique();
            $table->string('source_signature', 64)->unique();
            $table->string('source_fingerprint', 64)->nullable()->index();
            $table->string('expression_fingerprint', 64)->nullable()->index();
            $table->string('semantic_fingerprint', 64)->nullable()->index();
            $table->unsignedInteger('source_line')->nullable()->index();
            $table->string('kind', 40)->index();
            $table->string('function_name', 80)->nullable()->index();
            $table->longText('raw_expression')->nullable();
            $table->longText('literal_text')->nullable();
            $table->longText('literal_text_suggested')->nullable();
            $table->text('found_translation_key')->nullable();
            $table->text('existing_key')->nullable();
            $table->text('suggested_key')->nullable();
            $table->string('namespace')->nullable();
            $table->string('group')->nullable();
            $table->string('path_key')->nullable();
            $table->string('scope')->nullable();
            $table->string('dynamic_scope')->nullable();
            $table->string('entry_type', 40)->nullable()->index();
            $table->string('candidate_type', 40)->nullable()->index();
            $table->string('candidate_reason', 120)->nullable()->index();
            $table->string('status', 40)->default('active')->index();
            $table->timestamp('first_seen_at')->nullable()->index();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->unsignedInteger('scan_count')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['source_file_id', 'source_line'], 'tw_findings_file_line_index');
            $table->index(['namespace', 'group'], 'tw_findings_namespace_group_index');
        });

        Schema::create('translation_workbench_keys', function (Blueprint $table): void {
            $table->id();
            $table->string('fingerprint', 64)->unique();
            $table->text('translation_key')->nullable();
            $table->text('suggested_key')->nullable();
            $table->string('namespace')->nullable();
            $table->string('group')->nullable();
            $table->string('path_key')->nullable();
            $table->string('scope')->nullable();
            $table->string('key_segment_domain')->nullable()->index();
            $table->string('key_segment_section')->nullable()->index();
            $table->string('key_segment_context')->nullable()->index();
            $table->string('key_segment_extra')->nullable()->index();
            $table->string('key_segment_name')->nullable()->index();
            $table->string('key_type', 40)->default('static')->index();
            $table->boolean('is_ui_key')->default(false)->index();
            $table->boolean('is_dynamic_key')->default(false)->index();
            $table->boolean('is_dynamic_multi')->default(false)->index();
            $table->string('status', 40)->default('open')->index();
            $table->string('review_status', 40)->default('pending')->index();
            $table->timestamp('reviewed_at')->nullable()->index();
            $table->foreignId('reviewed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['namespace', 'group'], 'tw_keys_namespace_group_index');
            $table->index([
                'namespace',
                'group',
                'key_segment_domain',
                'key_segment_section',
                'key_segment_context',
            ], 'tw_keys_segment_filter_index');
        });

        Schema::create('translation_workbench_key_findings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('key_id')
                ->constrained('translation_workbench_keys')
                ->cascadeOnDelete();
            $table->foreignId('finding_id')
                ->constrained('translation_workbench_findings')
                ->cascadeOnDelete();
            $table->string('relation_type', 40)->default('candidate')->index();
            $table->string('status', 40)->default('active')->index();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['key_id', 'finding_id', 'relation_type'], 'tw_key_findings_unique');
        });

        Schema::create('translation_workbench_key_values', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('key_id')
                ->constrained('translation_workbench_keys')
                ->cascadeOnDelete();
            $table->string('locale', 20)->index();
            $table->longText('value')->nullable();
            $table->string('status', 40)->default('missing')->index();
            $table->string('source', 80)->default('translation_workbench')->index();
            $table->timestamp('reviewed_at')->nullable()->index();
            $table->foreignId('reviewed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['key_id', 'locale'], 'tw_key_values_unique');
        });

        Schema::create('translation_workbench_dynamic_key_values', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('key_id')
                ->constrained('translation_workbench_keys')
                ->cascadeOnDelete();
            $table->string('value_key')->index();
            $table->string('locale', 20)->index();
            $table->longText('value')->nullable();
            $table->longText('native_label')->nullable();
            $table->string('status', 40)->default('missing')->index();
            $table->string('source', 80)->default('translation_workbench')->index();
            $table->timestamp('reviewed_at')->nullable()->index();
            $table->foreignId('reviewed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['key_id', 'value_key', 'locale'], 'tw_dynamic_key_values_unique');
        });

        Schema::create('translation_workbench_reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('key_id')
                ->nullable()
                ->constrained('translation_workbench_keys')
                ->cascadeOnDelete();
            $table->foreignId('finding_id')
                ->nullable()
                ->constrained('translation_workbench_findings')
                ->cascadeOnDelete();
            $table->string('review_type', 80)->index();
            $table->string('decision', 80)->index();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('meta')->nullable();
            $table->foreignId('reviewed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('translation_workbench_timeline_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('key_id')
                ->nullable()
                ->constrained('translation_workbench_keys')
                ->cascadeOnDelete();
            $table->foreignId('finding_id')
                ->nullable()
                ->constrained('translation_workbench_findings')
                ->cascadeOnDelete();
            $table->foreignId('review_id')
                ->nullable()
                ->constrained('translation_workbench_reviews')
                ->nullOnDelete();
            $table->string('event_type', 120)->index();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('context')->nullable();
            $table->foreignId('created_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_workbench_timeline_events');
        Schema::dropIfExists('translation_workbench_reviews');
        Schema::dropIfExists('translation_workbench_dynamic_key_values');
        Schema::dropIfExists('translation_workbench_key_values');
        Schema::dropIfExists('translation_workbench_key_findings');
        Schema::dropIfExists('translation_workbench_keys');
        Schema::dropIfExists('translation_workbench_findings');
        Schema::dropIfExists('translation_workbench_source_files');
    }
};
