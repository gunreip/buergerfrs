<?php

use App\Models\User;
use Gunreip\TranslationWorkbench\Support\WorkbenchUserSummary;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class);

it('reads only public account details and current roles for the requested actor', function () {
    $user = new User;
    $user->forceFill(['id' => 9, 'name' => 'Preparing Person', 'email' => 'preparer@example.test', 'password' => 'secret']);
    $user->setRelation('roles', collect([new Role(['name' => 'Admin'])]));
    $provider = Mockery::mock(UserProvider::class);
    $provider->shouldReceive('retrieveById')->once()->with(9)->andReturn($user);
    Auth::shouldReceive('getProvider')->once()->andReturn($provider);

    expect((new WorkbenchUserSummary)->find(9))->toBe([
        'id' => 9, 'name' => 'Preparing Person', 'email' => 'preparer@example.test', 'roles' => ['Admin'],
    ]);
});

it('returns no account details when the preparing account no longer exists', function () {
    $provider = Mockery::mock(UserProvider::class);
    $provider->shouldReceive('retrieveById')->once()->with(9)->andReturn(null);
    Auth::shouldReceive('getProvider')->once()->andReturn($provider);

    expect((new WorkbenchUserSummary)->find(9))->toBeNull();
});
