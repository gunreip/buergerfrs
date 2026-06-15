<?php

// database/migrations/2026_06_10_060337_create_translation_usage_audit_decisions_table.php

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
        Schema::create('translation_usage_audit_decisions', function (Blueprint $table) {
            $table->id();

            $table->string('audit_type', 32);
            $table->string('normalized_value_hash', 64);
            $table->text('normalized_value')->nullable();
            $table->string('source_locale', 32)->default('en');
            $table->text('source_value')->nullable();

            $table->string('suggested_translation_key')->nullable();
            $table->string('target_translation_key')->nullable();

            $table->string('decision_action', 64)->default('undecided');
            $table->string('decision_status', 64)->default('draft');

            $table->text('review_note')->nullable();
            $table->jsonb('snapshot')->nullable();

            $table->foreignId('reviewed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->unique(['audit_type', 'normalized_value_hash'], 'tuad_audit_type_value_hash_unique');
            $table->index('decision_action', 'tuad_decision_action_index');
            $table->index('decision_status', 'tuad_decision_status_index');
            $table->index('target_translation_key', 'tuad_target_translation_key_index');
            $table->index('reviewed_by_user_id', 'tuad_reviewed_by_user_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_usage_audit_decisions');
    }
};
