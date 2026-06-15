<?php

// database/migrations/2026_06_10_060338_create_translation_usage_audit_decision_usages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('translation_usage_audit_decision_usages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('translation_usage_audit_decision_id')
                ->constrained('translation_usage_audit_decisions')
                ->cascadeOnDelete();

            $table->foreignId('translation_key_id')
                ->nullable()
                ->constrained('translation_keys')
                ->nullOnDelete();

            $table->string('current_translation_key')->nullable();
            $table->string('target_translation_key')->nullable();

            $table->text('file')->nullable();
            $table->unsignedInteger('line')->nullable();
            $table->string('detected_function', 64)->nullable();
            $table->string('classification', 64)->nullable();
            $table->string('reason')->nullable();
            $table->boolean('is_stale')->default(false);

            $table->text('raw')->nullable();
            $table->text('original_raw')->nullable();

            $table->boolean('include_in_change')->default(true);
            $table->string('change_status', 64)->default('pending');

            $table->timestamps();

            $table->index('translation_usage_audit_decision_id', 'tuadu_decision_id_index');
            $table->index('translation_key_id', 'tuadu_translation_key_id_index');
            $table->index('target_translation_key', 'tuadu_target_translation_key_index');
            $table->index('change_status', 'tuadu_change_status_index');
            $table->index('include_in_change', 'tuadu_include_in_change_index');
            $table->index('is_stale', 'tuadu_is_stale_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_usage_audit_decision_usages');
    }
};
