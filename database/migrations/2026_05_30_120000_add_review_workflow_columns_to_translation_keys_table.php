<?php

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
        Schema::table('translation_keys', function (Blueprint $table): void {
            $table->string('workflow_status', 32)
                ->default('open')
                ->after('status')
                ->index();

            $table->timestamp('reviewed_at')
                ->nullable()
                ->after('obsolete_at')
                ->index();

            $table->foreignId('reviewed_by_user_id')
                ->nullable()
                ->after('reviewed_at')
                ->constrained('users')
                ->nullOnDelete();

            $table->text('review_note')
                ->nullable()
                ->after('reviewed_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('translation_keys', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('reviewed_by_user_id');
            $table->dropColumn('reviewed_at');
            $table->dropColumn('review_note');
            $table->dropColumn('workflow_status');
        });
    }
};
