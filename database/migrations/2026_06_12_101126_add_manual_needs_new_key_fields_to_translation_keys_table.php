<?php

// database/migrations/2026_06_12_101126_add_manual_needs_new_key_fields_to_translation_keys_table.php

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
        Schema::table('translation_keys', function (Blueprint $table) {
            $table->timestamp('needs_new_key_marked_at')
                ->nullable()
                ->after('review_note');

            $table->foreignId('needs_new_key_marked_by_user_id')
                ->nullable()
                ->after('needs_new_key_marked_at')
                ->constrained('users')
                ->nullOnDelete();

            $table->text('needs_new_key_note')
                ->nullable()
                ->after('needs_new_key_marked_by_user_id');

            $table->timestamp('needs_new_key_resolved_at')
                ->nullable()
                ->after('needs_new_key_note');

            $table->index('needs_new_key_marked_at', 'translation_keys_needs_new_key_marked_at_index');
            $table->index('needs_new_key_resolved_at', 'translation_keys_needs_new_key_resolved_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('translation_keys', function (Blueprint $table) {
            $table->dropIndex('translation_keys_needs_new_key_marked_at_index');
            $table->dropIndex('translation_keys_needs_new_key_resolved_at_index');

            $table->dropConstrainedForeignId('needs_new_key_marked_by_user_id');

            $table->dropColumn([
                'needs_new_key_marked_at',
                'needs_new_key_note',
                'needs_new_key_resolved_at',
            ]);
        });
    }
};
