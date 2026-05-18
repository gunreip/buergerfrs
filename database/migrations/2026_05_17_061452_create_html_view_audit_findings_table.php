<?php

// database/migrations/2026_05_17_061452_create_html_view_audit_findings_table.php

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
        Schema::create('html_view_audit_findings', function (Blueprint $table) {
            $table->id();

            $table->string('fingerprint', 64)->unique();
            $table->string('source_fingerprint', 64)->index();
            $table->foreignId('previous_finding_id')
                ->nullable()
                ->constrained('html_view_audit_findings')
                ->nullOnDelete();

            $table->string('status', 32)->default('open')->index();

            $table->string('section', 64)->index();
            $table->string('type', 64)->index();
            $table->text('file');
            $table->string('tag', 128)->nullable();
            $table->string('closing_tag', 128)->nullable();

            $table->unsignedInteger('opened_line')->nullable();
            $table->unsignedInteger('closing_line')->nullable();

            $table->text('expected_closing')->nullable();
            $table->text('actual_closing')->nullable();

            $table->timestamp('first_seen_at')->nullable()->index();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->timestamp('resolved_at')->nullable()->index();
            $table->string('resolved_source', 64)->nullable();

            $table->timestamp('ignored_at')->nullable()->index();
            $table->foreignId('ignored_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('comment')->nullable();
            $table->jsonb('snapshot_payload')->nullable();

            $table->timestamps();

            $table->index(['status', 'section']);
            $table->index(['status', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('html_view_audit_findings');
    }
};
