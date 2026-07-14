<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_workbench_dynamic_values', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('entry_id')
                ->constrained('translation_workbench_entries')
                ->cascadeOnDelete();
            $table->string('value_key')->index();
            $table->string('locale', 20)->index();
            $table->longText('value')->nullable();
            $table->string('status', 40)->default('missing')->index();
            $table->string('source', 40)->default('translation_workbench')->index();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['entry_id', 'value_key', 'locale'], 'tw_dynamic_values_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_workbench_dynamic_values');
    }
};
