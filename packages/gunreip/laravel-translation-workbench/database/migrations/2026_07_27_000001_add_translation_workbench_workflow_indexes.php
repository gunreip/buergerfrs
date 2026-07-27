<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE INDEX IF NOT EXISTS tw_key_findings_status_finding_key_index ON translation_workbench_key_findings (status, finding_id, key_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS tw_key_findings_status_key_finding_index ON translation_workbench_key_findings (status, key_id, finding_id)');

        DB::statement('CREATE INDEX IF NOT EXISTS tw_lang_values_locale_status_translation_key_index ON translation_workbench_lang_values (locale, status, translation_key)');

        DB::statement('CREATE INDEX IF NOT EXISTS tw_reviews_decision_finding_key_index ON translation_workbench_reviews (decision, finding_id, key_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS tw_reviews_type_finding_key_reviewed_index ON translation_workbench_reviews (review_type, finding_id, key_id, reviewed_at)');

        DB::statement('CREATE INDEX IF NOT EXISTS tw_timeline_finding_created_index ON translation_workbench_timeline_events (finding_id, created_at)');
        DB::statement('CREATE INDEX IF NOT EXISTS tw_timeline_key_created_index ON translation_workbench_timeline_events (key_id, created_at)');
        DB::statement('CREATE INDEX IF NOT EXISTS tw_timeline_review_created_index ON translation_workbench_timeline_events (review_id, created_at)');

        DB::statement('CREATE INDEX IF NOT EXISTS tw_dynamic_sources_finding_status_index ON translation_workbench_dynamic_sources (finding_id, status)');
        DB::statement('CREATE INDEX IF NOT EXISTS tw_dynamic_sources_key_status_index ON translation_workbench_dynamic_sources (key_id, status)');
        DB::statement('CREATE INDEX IF NOT EXISTS tw_dynamic_source_values_source_status_index ON translation_workbench_dynamic_source_values (dynamic_source_id, status)');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS tw_dynamic_source_values_source_status_index');
        DB::statement('DROP INDEX IF EXISTS tw_dynamic_sources_key_status_index');
        DB::statement('DROP INDEX IF EXISTS tw_dynamic_sources_finding_status_index');

        DB::statement('DROP INDEX IF EXISTS tw_timeline_review_created_index');
        DB::statement('DROP INDEX IF EXISTS tw_timeline_key_created_index');
        DB::statement('DROP INDEX IF EXISTS tw_timeline_finding_created_index');

        DB::statement('DROP INDEX IF EXISTS tw_reviews_type_finding_key_reviewed_index');
        DB::statement('DROP INDEX IF EXISTS tw_reviews_decision_finding_key_index');

        DB::statement('DROP INDEX IF EXISTS tw_lang_values_locale_status_translation_key_index');

        DB::statement('DROP INDEX IF EXISTS tw_key_findings_status_key_finding_index');
        DB::statement('DROP INDEX IF EXISTS tw_key_findings_status_finding_key_index');
    }
};
