<?php

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
        Schema::table('roles', function (Blueprint $table): void {
            $table->string('category')->nullable()->after('guard_name');
            $table->unsignedSmallInteger('sort_order')->default(100)->after('category');
            $table->text('description')->nullable()->after('sort_order');
            $table->boolean('is_system')->default(false)->after('description');
            $table->boolean('is_assignable')->default(true)->after('is_system');

            $table->index(['category', 'sort_order'], 'roles_category_sort_order_index');
            $table->index('is_assignable', 'roles_is_assignable_index');
        });

        DB::table('roles')
            ->where('name', 'Super-Admin')
            ->update([
                'category' => 'system',
                'sort_order' => 10,
                'description' => 'Full system administration role with unrestricted access.',
                'is_system' => true,
                'is_assignable' => true,
            ]);

        DB::table('roles')
            ->where('name', 'Admin')
            ->update([
                'category' => 'system',
                'sort_order' => 20,
                'description' => 'Administration role for managing operational areas.',
                'is_system' => true,
                'is_assignable' => true,
            ]);

        DB::table('roles')
            ->where('name', 'User')
            ->update([
                'category' => 'user',
                'sort_order' => 100,
                'description' => 'Default user role for regular application access.',
                'is_system' => true,
                'is_assignable' => true,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table): void {
            $table->dropIndex('roles_category_sort_order_index');
            $table->dropIndex('roles_is_assignable_index');

            $table->dropColumn([
                'category',
                'sort_order',
                'description',
                'is_system',
                'is_assignable',
            ]);
        });
    }
};
