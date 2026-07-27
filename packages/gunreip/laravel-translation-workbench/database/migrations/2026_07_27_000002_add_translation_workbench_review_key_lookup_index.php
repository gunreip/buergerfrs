<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE INDEX IF NOT EXISTS tw_reviews_decision_key_finding_index ON translation_workbench_reviews (decision, key_id, finding_id)');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS tw_reviews_decision_key_finding_index');
    }
};
