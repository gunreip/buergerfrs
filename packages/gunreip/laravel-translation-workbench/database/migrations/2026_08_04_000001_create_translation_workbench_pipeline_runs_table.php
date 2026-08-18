<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_workbench_pipeline_runs', function (Blueprint $table): void {
            $table->id();
            $table->string('command')->index();
            $table->json('options')->nullable();
            $table->string('status', 40)->default('pending')->index();
            $table->unsignedInteger('current_step')->default(0);
            $table->unsignedInteger('total_steps')->default(0);
            $table->string('current_step_label')->nullable();
            $table->string('current_step_command')->nullable();
            $table->integer('exit_code')->nullable();
            $table->text('error_message')->nullable();
            $table->json('summary')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('started_at')->nullable()->index();
            $table->timestamp('finished_at')->nullable()->index();
            $table->timestamps();

            $table->index(['status', 'created_at'], 'tw_pipeline_runs_status_created_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_workbench_pipeline_runs');
    }
};
