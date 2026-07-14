<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_workbench_entries', function (Blueprint $table): void {
            $table->id();
            $table->string('fingerprint', 64)->unique();
            $table->string('source_signature', 64)->unique();
            $table->string('kind', 40)->index();
            $table->string('source_type', 40)->index();
            $table->string('source_path')->index();
            $table->unsignedInteger('source_line')->nullable();
            $table->string('function_name', 80)->nullable()->index();
            $table->text('raw_expression')->nullable();
            $table->text('literal_text')->nullable();
            $table->text('translation_key')->nullable();
            $table->text('suggested_key')->nullable();
            $table->text('namespace')->nullable();
            $table->text('group')->nullable();
            $table->string('status', 40)->default('open')->index();
            $table->string('review_status', 40)->default('pending')->index();
            $table->timestamp('first_seen_at')->nullable()->index();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->unsignedInteger('scan_count')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['status', 'review_status', 'kind'], 'tw_entries_status_review_kind_index');
            $table->index(['source_path', 'source_line'], 'tw_entries_source_position_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_workbench_entries');
    }
};
