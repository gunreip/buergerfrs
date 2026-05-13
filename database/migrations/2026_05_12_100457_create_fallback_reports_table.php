<?php

// database/migrations/2026_05_12_100457_create_fallback_reports_table.php

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
        Schema::create('fallback_reports', function (Blueprint $table) {
            $table->id();

            $table->string('type')->index();
            $table->string('key')->index();
            $table->string('fallback')->nullable();
            $table->string('fingerprint', 64)->index();

            $table->jsonb('context')->nullable();

            $table->unsignedInteger('count')->default(1);
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();

            $table->boolean('reviewed')->default(false)->index();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('review_note')->nullable();

            $table->timestamps();

            $table->index(['fingerprint', 'reviewed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fallback_reports');
    }
};
