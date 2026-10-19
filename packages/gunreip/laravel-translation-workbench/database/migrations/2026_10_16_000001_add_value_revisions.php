<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Database-owned revisions also cover query-builder imports and bulk updates.
    private const FIELDS = [
        'translation_workbench_lang_values' => ['locale', 'namespace', 'lang_key', 'translation_key', 'value', 'value_type', 'status'],
        'translation_workbench_dynamic_key_values' => ['key_id', 'value_key', 'locale', 'value', 'native_label', 'status'],
    ];

    public function up(): void
    {
        if (! in_array(DB::getDriverName(), ['pgsql', 'sqlite'], true)) {
            throw new RuntimeException('Workbench value revisions require PostgreSQL or SQLite.');
        }
        foreach (self::FIELDS as $table => $fields) {
            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->unsignedBigInteger('value_revision')->default(1));
            $trigger = $table.'_revision';
            if (DB::getDriverName() === 'pgsql') {
                $changes = implode(' OR ', array_map(fn ($field) => "NEW.$field IS DISTINCT FROM OLD.$field", $fields));
                DB::unprepared("CREATE FUNCTION {$trigger}() RETURNS trigger LANGUAGE plpgsql AS \$\$
                    BEGIN
                        NEW.value_revision := OLD.value_revision + CASE WHEN $changes THEN 1 ELSE 0 END;
                        RETURN NEW;
                    END; \$\$");
                DB::unprepared("CREATE TRIGGER $trigger BEFORE UPDATE ON $table FOR EACH ROW EXECUTE FUNCTION {$trigger}()");
            } else {
                $changes = implode(' OR ', array_map(fn ($field) => "NEW.$field IS NOT OLD.$field", $fields));
                $columns = implode(', ', $fields);
                // The internal counter update does not name any watched field, avoiding recursion.
                DB::unprepared("CREATE TRIGGER $trigger AFTER UPDATE OF $columns ON $table
                    FOR EACH ROW WHEN $changes
                    BEGIN UPDATE $table SET value_revision = OLD.value_revision + 1 WHERE id = NEW.id; END");
            }
        }
    }

    public function down(): void
    {
        foreach (self::FIELDS as $table => $fields) {
            $trigger = $table.'_revision';
            if (DB::getDriverName() === 'pgsql') {
                DB::unprepared("DROP TRIGGER IF EXISTS $trigger ON $table");
                DB::unprepared("DROP FUNCTION IF EXISTS {$trigger}()");
            } else {
                DB::unprepared("DROP TRIGGER IF EXISTS $trigger");
            }
            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropColumn('value_revision'));
        }
    }
};
