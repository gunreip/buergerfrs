<?php

// php artisan db:seed --class=RolesAndPermissionsSeeder

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Spatie Models
        $roleModel = app(config('permission.models.role'));
        $permissionModel = app(config('permission.models.permission'));

        // Rollen
        $roles = [
            'Super-Admin',
            'Admin',
            'User',
        ];

        // Rechte
        $permissions = [
            'user.manage',
            'settings.edit',
            'logs.view',
        ];

        // Anlegen
        foreach ($roles as $role) {
            $roleModel::findOrCreate($role);
        }
        foreach ($permissions as $permission) {
            $permissionModel::findOrCreate($permission);
        }

        // Rechte zuweisen
        $roleModel::findByName('Super-Admin')->givePermissionTo($permissions);
        $roleModel::findByName('Admin')->givePermissionTo(['user.manage', 'settings.edit']);
        $roleModel::findByName('User')->givePermissionTo([]);
    }
}
