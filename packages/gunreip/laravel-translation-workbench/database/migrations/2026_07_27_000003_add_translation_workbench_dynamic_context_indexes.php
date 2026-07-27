<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE INDEX IF NOT EXISTS tw_dynamic_key_values_key_locale_status_value_index ON translation_workbench_dynamic_key_values (key_id, locale, status, value_key)');

        DB::statement('CREATE INDEX IF NOT EXISTS tw_option_discoveries_status_suggested_key_index ON translation_workbench_option_discoveries (status, suggested_key)');
        DB::statement('CREATE INDEX IF NOT EXISTS tw_option_discoveries_status_workbench_key_index ON translation_workbench_option_discoveries (status, workbench_suggested_key)');
        DB::statement('CREATE INDEX IF NOT EXISTS tw_option_discoveries_status_dynamic_key_index ON translation_workbench_option_discoveries (status, suggested_dynamic_key)');
        DB::statement('CREATE INDEX IF NOT EXISTS tw_option_discoveries_status_source_position_index ON translation_workbench_option_discoveries (status, source_path, source_line)');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS tw_option_discoveries_status_source_position_index');
        DB::statement('DROP INDEX IF EXISTS tw_option_discoveries_status_dynamic_key_index');
        DB::statement('DROP INDEX IF EXISTS tw_option_discoveries_status_workbench_key_index');
        DB::statement('DROP INDEX IF EXISTS tw_option_discoveries_status_suggested_key_index');

        DB::statement('DROP INDEX IF EXISTS tw_dynamic_key_values_key_locale_status_value_index');
    }
};
