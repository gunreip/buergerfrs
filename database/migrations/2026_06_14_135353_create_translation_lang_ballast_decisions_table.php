<?php

// database/migrations/2026_06_14_135353_create_translation_lang_ballast_decisions_table.php

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
        Schema::create('translation_lang_ballast_decisions', function (Blueprint $table) {
            $table->id();

            $table->string('locale', 20);
            $table->string('namespace', 160);
            $table->string('group', 160)->nullable();
            $table->text('key');
            $table->string('file', 500);
            $table->text('file_key');
            $table->string('value_hash', 64);
            $table->string('candidate_hash', 64)->unique();

            $table
                ->foreignId('translation_key_id')
                ->nullable()
                ->constrained('translation_keys')
                ->nullOnDelete();

            $table
                ->foreignId('translation_value_id')
                ->nullable()
                ->constrained('translation_values')
                ->nullOnDelete();

            $table->string('action_candidate', 40);
            $table->string('decision_status', 40)->default('open');
            $table->text('decision_note')->nullable();

            $table->string('reason_detail', 160)->nullable();
            $table->string('lang_file_action_reason', 160)->nullable();

            $table->timestamp('reviewed_at')->nullable();

            $table
                ->foreignId('reviewed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['locale', 'namespace']);
            $table->index(['action_candidate', 'decision_status']);
            $table->index(['translation_key_id']);
            $table->index(['translation_value_id']);
            $table->index(['reviewed_by_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_lang_ballast_decisions');
    }
};
