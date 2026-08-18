<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_workbench_pipeline_run_steps', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pipeline_run_id')
                ->constrained('translation_workbench_pipeline_runs')
                ->cascadeOnDelete();
            $table->unsignedInteger('step_number');
            $table->unsignedInteger('total_steps')->default(0);
            $table->string('label');
            $table->string('command')->index();
            $table->json('arguments')->nullable();
            $table->string('status', 40)->default('pending')->index();
            $table->integer('exit_code')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->text('error_message')->nullable();
            $table->json('summary')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('started_at')->nullable()->index();
            $table->timestamp('finished_at')->nullable()->index();
            $table->timestamps();

            $table->unique(['pipeline_run_id', 'step_number'], 'tw_pipeline_run_steps_run_number_unique');
            $table->index(['pipeline_run_id', 'status'], 'tw_pipeline_run_steps_run_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_workbench_pipeline_run_steps');
    }
};
