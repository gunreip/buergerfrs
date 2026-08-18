<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_workbench_key_inventory', function (Blueprint $table): void {
            $table->id();
            $table->text('translation_key');
            $table->text('normalized_translation_key');
            $table->string('namespace')->nullable()->index();
            $table->string('group')->nullable()->index();
            $table->string('key_type', 40)->nullable()->index();
            $table->string('inventory_status', 40)->default('active')->index();
            $table->unsignedInteger('key_record_count')->default(0);
            $table->unsignedInteger('reviewed_key_count')->default(0);
            $table->unsignedInteger('finding_active_count')->default(0);
            $table->unsignedInteger('finding_commented_out_count')->default(0);
            $table->unsignedInteger('finding_obsolete_count')->default(0);
            $table->unsignedInteger('relation_active_count')->default(0);
            $table->unsignedInteger('relation_commented_out_count')->default(0);
            $table->unsignedInteger('relation_obsolete_count')->default(0);
            $table->unsignedInteger('source_value_active_count')->default(0);
            $table->unsignedInteger('source_value_obsolete_count')->default(0);
            $table->unsignedInteger('source_value_deleted_count')->default(0);
            $table->unsignedInteger('target_value_active_count')->default(0);
            $table->unsignedInteger('target_value_obsolete_count')->default(0);
            $table->unsignedInteger('target_value_deleted_count')->default(0);
            $table->unsignedInteger('lang_file_locale_count')->default(0);
            $table->unsignedInteger('workbench_value_count')->default(0);
            $table->unsignedInteger('dynamic_value_count')->default(0);
            $table->unsignedInteger('dynamic_source_count')->default(0);
            $table->unsignedInteger('shared_finding_count')->default(0);
            $table->boolean('is_shared')->default(false)->index();
            $table->boolean('is_ui')->default(false)->index();
            $table->boolean('is_dynamic')->default(false)->index();
            $table->boolean('is_dynamic_multi')->default(false)->index();
            $table->boolean('has_active_code_usage')->default(false)->index();
            $table->boolean('has_only_obsolete_code_usage')->default(false)->index();
            $table->boolean('has_lang_values')->default(false)->index();
            $table->boolean('is_orphaned_lang_value')->default(false)->index();
            $table->boolean('candidate_for_lang_delete')->default(false)->index();
            $table->timestamp('first_seen_at')->nullable()->index();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->unsignedInteger('scan_count')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique('normalized_translation_key', 'tw_key_inventory_normalized_unique');
            $table->index(['namespace', 'group', 'inventory_status'], 'tw_key_inventory_namespace_group_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_workbench_key_inventory');
    }
};
