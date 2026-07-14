<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_workbench_duplicate_candidates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('entry_id')
                ->constrained('translation_workbench_entries')
                ->cascadeOnDelete();
            $table->string('duplicate_type', 80)->index();
            $table->string('group_fingerprint', 64)->index();
            $table->string('confidence', 40)->index();
            $table->unsignedInteger('group_size')->default(0)->index();
            $table->json('matched_entry_ids')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['entry_id', 'duplicate_type', 'group_fingerprint'], 'tw_duplicate_candidates_unique');
            $table->index(['duplicate_type', 'confidence'], 'tw_duplicate_candidates_type_confidence_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_workbench_duplicate_candidates');
    }
};
