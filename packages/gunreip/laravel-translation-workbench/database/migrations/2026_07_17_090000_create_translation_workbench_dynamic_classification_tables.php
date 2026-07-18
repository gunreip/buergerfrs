<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_workbench_dynamic_sources', function (Blueprint $table): void {
            $table->id();
            $table->string('fingerprint', 64)->unique();
            $table->foreignId('key_id')
                ->nullable()
                ->constrained('translation_workbench_keys')
                ->cascadeOnDelete();
            $table->foreignId('finding_id')
                ->nullable()
                ->constrained('translation_workbench_findings')
                ->cascadeOnDelete();
            $table->foreignId('option_discovery_id')
                ->nullable()
                ->constrained('translation_workbench_option_discoveries')
                ->nullOnDelete();
            $table->string('classification', 80)->default('unknown')->index();
            $table->string('cardinality', 40)->default('unknown')->index();
            $table->string('origin', 40)->default('unknown')->index();
            $table->string('source_type', 80)->nullable()->index();
            $table->string('source_reference')->nullable();
            $table->string('source_path')->nullable()->index();
            $table->unsignedInteger('source_line')->nullable()->index();
            $table->longText('source_expression')->nullable();
            $table->string('dynamic_scope')->nullable()->index();
            $table->unsignedInteger('values_count')->default(0)->index();
            $table->string('confidence', 40)->default('low')->index();
            $table->string('status', 40)->default('needs_review')->index();
            $table->timestamp('first_seen_at')->nullable()->index();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['classification', 'status'], 'tw_dynamic_sources_classification_status_index');
            $table->index(['key_id', 'finding_id'], 'tw_dynamic_sources_key_finding_index');
        });

        Schema::create('translation_workbench_dynamic_source_values', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('dynamic_source_id')
                ->constrained('translation_workbench_dynamic_sources')
                ->cascadeOnDelete();
            $table->string('value_key')->index();
            $table->longText('native_label')->nullable();
            $table->string('origin', 40)->default('unknown')->index();
            $table->string('translation_key')->nullable()->index();
            $table->string('status', 40)->default('active')->index();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['dynamic_source_id', 'value_key'], 'tw_dynamic_source_values_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_workbench_dynamic_source_values');
        Schema::dropIfExists('translation_workbench_dynamic_sources');
    }
};
