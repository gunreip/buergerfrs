<?php

// database/migrations/2026_05_09_074601_create_person_documents_table.php

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
        Schema::create('person_documents', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('person_id')
                ->constrained('people')
                ->cascadeOnDelete();

            $table->foreignId('person_identifier_id')
                ->nullable()
                ->constrained('person_identifiers')
                ->nullOnDelete();

            $table->foreignId('issuing_country_id')
                ->nullable()
                ->constrained('countries')
                ->nullOnDelete();

            $table->string('type');
            $table->string('title')->nullable();
            $table->string('document_number')->nullable();
            $table->string('issuing_authority')->nullable();

            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();

            $table->string('file_disk')->nullable();
            $table->string('file_path')->nullable();
            $table->string('original_filename')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            $table->timestamp('verified_at')->nullable();

            $table->foreignId('verified_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('person_id');
            $table->index('person_identifier_id');
            $table->index('issuing_country_id');
            $table->index('type');
            $table->index('document_number');
            $table->index('issued_at');
            $table->index('expires_at');
            $table->index('file_disk');
            $table->index('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_documents');
    }
};
