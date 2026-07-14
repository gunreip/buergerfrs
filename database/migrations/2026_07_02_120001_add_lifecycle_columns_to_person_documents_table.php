<?php

// database/migrations/2026_07_02_120001_add_lifecycle_columns_to_person_documents_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('person_documents', function (Blueprint $table): void {
            $table->foreignId('person_correspondence_id')
                ->nullable()
                ->constrained('person_correspondences')
                ->nullOnDelete();

            $table->string('status')->default('active');
            $table->string('category')->default('other');
            $table->string('source')->default('upload');
            $table->string('direction')->default('none');

            $table->date('document_date')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();

            $table->boolean('is_current')->default(true);

            $table->foreignId('replaces_document_id')
                ->nullable()
                ->constrained('person_documents')
                ->nullOnDelete();

            $table->foreignId('replaced_by_document_id')
                ->nullable()
                ->constrained('person_documents')
                ->nullOnDelete();

            $table->timestamp('archived_at')->nullable();
            $table->string('archived_reason')->nullable();

            $table->foreignId('created_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->index('person_correspondence_id');
            $table->index('status');
            $table->index('category');
            $table->index('source');
            $table->index('direction');
            $table->index('document_date');
            $table->index('received_at');
            $table->index('sent_at');
            $table->index('valid_from');
            $table->index('valid_until');
            $table->index('is_current');
            $table->index('replaces_document_id');
            $table->index('replaced_by_document_id');
            $table->index('archived_at');
            $table->index('created_by_user_id');
        });

        DB::table('person_documents')
            ->whereNotNull('issued_at')
            ->update(['valid_from' => DB::raw('issued_at')]);

        DB::table('person_documents')
            ->whereNotNull('expires_at')
            ->update(['valid_until' => DB::raw('expires_at')]);

        DB::table('person_documents')
            ->whereIn('type', ['id_card_copy', 'passport_copy'])
            ->update(['category' => 'identity']);

        DB::table('person_documents')
            ->where('type', 'residence_permit_copy')
            ->update(['category' => 'residence']);

        DB::table('person_documents')
            ->where('type', 'health_insurance_proof')
            ->update(['category' => 'insurance']);

        DB::table('person_documents')
            ->where('type', 'tax_document')
            ->update(['category' => 'tax']);

        DB::table('person_documents')
            ->whereNull('file_path')
            ->update(['source' => 'manual']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('person_documents', function (Blueprint $table): void {
            $table->dropForeign(['person_correspondence_id']);
            $table->dropForeign(['replaces_document_id']);
            $table->dropForeign(['replaced_by_document_id']);
            $table->dropForeign(['created_by_user_id']);

            $table->dropColumn([
                'person_correspondence_id',
                'status',
                'category',
                'source',
                'direction',
                'document_date',
                'received_at',
                'sent_at',
                'valid_from',
                'valid_until',
                'is_current',
                'replaces_document_id',
                'replaced_by_document_id',
                'archived_at',
                'archived_reason',
                'created_by_user_id',
            ]);
        });
    }
};
