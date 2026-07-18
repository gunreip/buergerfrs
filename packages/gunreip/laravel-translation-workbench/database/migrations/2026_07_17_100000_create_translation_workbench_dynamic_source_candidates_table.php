<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_workbench_dynamic_source_candidates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('dynamic_source_id')
                ->constrained('translation_workbench_dynamic_sources')
                ->cascadeOnDelete();
            $table->foreignId('key_id')
                ->nullable()
                ->constrained('translation_workbench_keys')
                ->nullOnDelete();
            $table->foreignId('finding_id')
                ->nullable()
                ->constrained('translation_workbench_findings')
                ->nullOnDelete();
            $table->string('suggested_key')->nullable()->index();
            $table->string('dynamic_scope')->nullable()->index();
            $table->longText('source_expression')->nullable();
            $table->string('candidate_source_type', 80)->index();
            $table->string('candidate_reference')->nullable()->index();
            $table->unsignedInteger('candidate_values_count')->default(0)->index();
            $table->json('candidate_values')->nullable();
            $table->string('confidence', 40)->default('low')->index();
            $table->string('review_status', 40)->default('pending')->index();
            $table->string('status', 40)->default('active')->index();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(
                ['dynamic_source_id', 'candidate_source_type', 'candidate_reference'],
                'tw_dynamic_source_candidates_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_workbench_dynamic_source_candidates');
    }
};
