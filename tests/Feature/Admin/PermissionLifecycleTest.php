<?php

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

it('reflects granted and revoked role permissions in Laravel authorization', function () {
    $user = User::factory()->create();
    $role = Role::findOrCreate('Admin', 'web');
    $permission = Permission::findOrCreate('translations.review', 'web');
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    expect($user->fresh()->can('translations.review'))->toBeTrue();

    $role->revokePermissionTo($permission);
    expect($user->fresh()->can('translations.review'))->toBeFalse();

    $user->givePermissionTo($permission);
    expect($user->fresh()->can('translations.review'))->toBeTrue();

    $user->revokePermissionTo($permission);
    expect($user->fresh()->can('translations.review'))->toBeFalse();
});

it('denies the administration middleware after persisted roles are replaced', function () {
    $user = User::factory()->create();
    Role::findOrCreate('Admin', 'web');
    Role::findOrCreate('User', 'web');
    $user->syncRoles(['Admin']);
    $this->actingAs($user->fresh());
    $check = fn () => app(RoleMiddleware::class)->handle(
        Request::create('/admin/translation-workbench'),
        fn () => 'allowed',
        'Admin|Super-Admin',
    );

    expect($check())->toBe('allowed');

    $user->syncRoles(['User']);
    $this->actingAs($user->fresh());
    expect($user->fresh()->getRoleNames()->all())->toBe(['User']);
    expect($check)->toThrow(UnauthorizedException::class);
});
