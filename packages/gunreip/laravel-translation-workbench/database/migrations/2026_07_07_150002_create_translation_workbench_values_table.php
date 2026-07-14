<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_workbench_values', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('entry_id')
                ->constrained('translation_workbench_entries')
                ->cascadeOnDelete();
            $table->string('value_key')->index();
            $table->text('native_label')->nullable();
            $table->string('source_type', 40)->nullable()->index();
            $table->string('source_reference')->nullable()->index();
            $table->string('status', 40)->default('open')->index();
            $table->timestamp('first_seen_at')->nullable()->index();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['entry_id', 'value_key'], 'tw_values_entry_value_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_workbench_values');
    }
};
