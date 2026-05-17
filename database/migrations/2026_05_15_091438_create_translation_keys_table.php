<?php

// database/migrations/2026_05_15_091438_create_translation_keys_table.php

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
        Schema::create('translation_keys', function (Blueprint $table) {
            $table->id();

            $table->string('fingerprint', 64)->unique();

            $table->string('key')->nullable()->index();
            $table->string('namespace')->nullable()->index();
            $table->string('group')->nullable()->index();

            $table->string('status', 32)->index();
            $table->string('classification', 32)->index();

            $table->string('source', 32)->default('audit')->index();

            $table->string('suggested_key')->nullable()->index();
            $table->text('native_text')->nullable();

            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('obsolete_at')->nullable();

            $table->timestamps();

            $table->index(['status', 'namespace']);
            $table->index(['classification', 'namespace']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_keys');
    }
};
