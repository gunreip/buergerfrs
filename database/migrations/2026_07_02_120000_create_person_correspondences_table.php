<?php

// database/migrations/2026_07_02_120000_create_person_correspondences_table.php

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
        Schema::create('person_correspondences', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('person_id')
                ->constrained('people')
                ->cascadeOnDelete();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('person_correspondences')
                ->nullOnDelete();

            $table->string('status')->default('open');
            $table->string('type')->default('general');
            $table->string('direction')->default('incoming');
            $table->string('channel')->default('letter');
            $table->string('source')->default('manual');
            $table->string('priority')->default('normal');

            $table->string('subject')->nullable();
            $table->text('summary')->nullable();
            $table->string('external_reference')->nullable();

            $table->date('document_date')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->foreignId('created_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('assigned_to_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('closed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('person_id');
            $table->index('parent_id');
            $table->index('status');
            $table->index('type');
            $table->index('direction');
            $table->index('channel');
            $table->index('source');
            $table->index('priority');
            $table->index('document_date');
            $table->index('received_at');
            $table->index('sent_at');
            $table->index('due_at');
            $table->index('closed_at');
            $table->index('created_by_user_id');
            $table->index('assigned_to_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_correspondences');
    }
};
