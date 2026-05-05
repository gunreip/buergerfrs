<?php

// database/migrations/2026_05_04_175850_add_metadata_to_permissions_table.php

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
        Schema::table('permissions', function (Blueprint $table): void {
            $table->string('category')
                ->nullable()
                ->after('guard_name');

            $table->unsignedInteger('sort_order')
                ->default(100)
                ->after('category');

            $table->text('description')
                ->nullable()
                ->after('sort_order');

            $table->boolean('is_system')
                ->default(false)
                ->after('description');
        });

        DB::table('permissions')
            ->where('name', 'user.manage')
            ->update([
                'category' => 'users',
                'sort_order' => 10,
                'description' => 'Allows managing application users.',
                'is_system' => true,
            ]);

        DB::table('permissions')
            ->where('name', 'settings.edit')
            ->update([
                'category' => 'settings',
                'sort_order' => 20,
                'description' => 'Allows editing application and administrative settings.',
                'is_system' => true,
            ]);

        DB::table('permissions')
            ->where('name', 'logs.view')
            ->update([
                'category' => 'system',
                'sort_order' => 30,
                'description' => 'Allows viewing system and activity logs.',
                'is_system' => true,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table): void {
            $table->dropColumn([
                'category',
                'sort_order',
                'description',
                'is_system',
            ]);
        });
    }
};
