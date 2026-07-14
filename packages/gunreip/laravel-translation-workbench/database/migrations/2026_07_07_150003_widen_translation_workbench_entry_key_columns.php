<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('translation_workbench_entries')) {
            return;
        }

        $this->dropIndexIfExists('translation_workbench_entries_translation_key_index');
        $this->dropIndexIfExists('translation_workbench_entries_suggested_key_index');
        $this->dropIndexIfExists('translation_workbench_entries_namespace_index');
        $this->dropIndexIfExists('translation_workbench_entries_group_index');

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE translation_workbench_entries ALTER COLUMN translation_key TYPE text');
            DB::statement('ALTER TABLE translation_workbench_entries ALTER COLUMN suggested_key TYPE text');
            DB::statement('ALTER TABLE translation_workbench_entries ALTER COLUMN namespace TYPE text');
            DB::statement('ALTER TABLE translation_workbench_entries ALTER COLUMN "group" TYPE text');

            return;
        }

        Schema::table('translation_workbench_entries', function ($table): void {
            $table->text('translation_key')->nullable()->change();
            $table->text('suggested_key')->nullable()->change();
            $table->text('namespace')->nullable()->change();
            $table->text('group')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('translation_workbench_entries')) {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE translation_workbench_entries ALTER COLUMN translation_key TYPE varchar(255)');
            DB::statement('ALTER TABLE translation_workbench_entries ALTER COLUMN suggested_key TYPE varchar(255)');
            DB::statement('ALTER TABLE translation_workbench_entries ALTER COLUMN namespace TYPE varchar(255)');
            DB::statement('ALTER TABLE translation_workbench_entries ALTER COLUMN "group" TYPE varchar(255)');
        } else {
            Schema::table('translation_workbench_entries', function ($table): void {
                $table->string('translation_key')->nullable()->change();
                $table->string('suggested_key')->nullable()->change();
                $table->string('namespace')->nullable()->change();
                $table->string('group')->nullable()->change();
            });
        }

        Schema::table('translation_workbench_entries', function ($table): void {
            $table->index('translation_key');
            $table->index('suggested_key');
            $table->index('namespace');
            $table->index('group');
        });
    }

    private function dropIndexIfExists(string $indexName): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("DROP INDEX IF EXISTS {$indexName}");

            return;
        }

        try {
            Schema::table('translation_workbench_entries', function ($table) use ($indexName): void {
                $table->dropIndex($indexName);
            });
        } catch (Throwable) {
            //
        }
    }
};
