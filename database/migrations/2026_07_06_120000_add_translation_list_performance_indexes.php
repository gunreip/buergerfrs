<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const STALE_AUDIT_USAGE_REASON = 'stale_audit_usage_not_seen_in_latest_sync';

    public function up(): void
    {
        Schema::table('translation_keys', function (Blueprint $table): void {
            $table->index(
                ['classification', 'updated_at', 'id'],
                'translation_keys_classification_updated_id_index',
            );

            $table->index(
                ['source', 'classification', 'updated_at', 'id'],
                'translation_keys_source_classification_updated_id_index',
            );
        });

        Schema::table('translation_usage_audit_decision_usages', function (Blueprint $table): void {
            $table->index(
                ['translation_key_id', 'change_status', 'translation_usage_audit_decision_id'],
                'tuadu_key_status_decision_index',
            );
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement(sprintf(
                "CREATE INDEX IF NOT EXISTS translation_usages_current_key_index
                ON translation_usages (translation_key_id)
                WHERE reason IS NULL OR reason <> '%s'",
                str_replace("'", "''", self::STALE_AUDIT_USAGE_REASON),
            ));

            return;
        }

        Schema::table('translation_usages', function (Blueprint $table): void {
            $table->index(
                ['translation_key_id', 'reason'],
                'translation_usages_key_reason_index',
            );
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS translation_usages_current_key_index');
        } else {
            Schema::table('translation_usages', function (Blueprint $table): void {
                $table->dropIndex('translation_usages_key_reason_index');
            });
        }

        Schema::table('translation_usage_audit_decision_usages', function (Blueprint $table): void {
            $table->dropIndex('tuadu_key_status_decision_index');
        });

        Schema::table('translation_keys', function (Blueprint $table): void {
            $table->dropIndex('translation_keys_source_classification_updated_id_index');
            $table->dropIndex('translation_keys_classification_updated_id_index');
        });
    }
};
