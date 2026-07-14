<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_workbench_option_discoveries', function (Blueprint $table): void {
            $table->id();
            $table->string('fingerprint', 64)->unique();
            $table->foreignId('matched_entry_id')
                ->nullable()
                ->constrained('translation_workbench_entries')
                ->nullOnDelete();
            $table->string('scope')->index();
            $table->string('suggested_dynamic_key')->nullable()->index();
            $table->string('workbench_suggested_key')->nullable()->index();
            $table->string('suggested_state', 80)->nullable()->index();
            $table->string('source_path')->index();
            $table->unsignedInteger('source_line')->nullable()->index();
            $table->string('options_variable')->index();
            $table->string('key_variable')->nullable();
            $table->string('label_variable')->nullable();
            $table->string('label_usage', 80)->nullable()->index();
            $table->string('source_type', 80)->nullable()->index();
            $table->string('source_reference')->nullable();
            $table->unsignedInteger('options_count')->default(0)->index();
            $table->string('status', 80)->default('open')->index();
            $table->timestamp('first_seen_at')->nullable()->index();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->json('options')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_workbench_option_discoveries');
    }
};
