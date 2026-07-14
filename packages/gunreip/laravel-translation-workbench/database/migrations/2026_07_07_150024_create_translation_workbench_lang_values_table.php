<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_workbench_lang_values', function (Blueprint $table): void {
            $table->id();
            $table->string('locale', 20)->index();
            $table->string('namespace')->index();
            $table->string('lang_key');
            $table->string('translation_key');
            $table->longText('value')->nullable();
            $table->string('value_type', 40)->default('string')->index();
            $table->string('source_path')->index();
            $table->string('source_hash', 64)->nullable()->index();
            $table->string('status', 40)->default('active')->index();
            $table->timestamp('first_seen_at')->nullable()->index();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->unsignedInteger('scan_count')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['locale', 'namespace', 'lang_key'], 'tw_lang_values_locale_namespace_key_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_workbench_lang_values');
    }
};
