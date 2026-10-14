<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Route;
use Livewire\Mechanisms\PersistentMiddleware\PersistentMiddleware;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Middleware\RoleMiddleware;
use Tests\TestCase;

uses(TestCase::class);

function translationPersistentMiddleware(string $routeName): array
{
    $route = Route::getRoutes()->getByName($routeName);
    expect($route)->not->toBeNull();
    $mechanism = new class extends PersistentMiddleware
    {
        public function forRequest(Request $request): array
        {
            return $this->getApplicablePersistentMiddleware($request);
        }
    };

    return $mechanism->forRequest(Request::create('/'.$route->uri()));
}

it('retains the administration role requirement on livewire follow-up requests', function (string $routeName) {
    expect(translationPersistentMiddleware($routeName))->toContain(RoleMiddleware::class.':Admin|Super-Admin');
})->with(['admin.translation-workbench', 'admin.translations', 'admin.translation-sub-languages']);

it('checks the current role again before allowing a follow-up action', function (string $role) {
    $user = Mockery::mock(User::class)->makePartial();
    $allowed = true;
    $user->shouldReceive('hasAnyRole')->with(['Admin', 'Super-Admin'])->andReturnUsing(function (array $roles) use (&$allowed, $role): bool {
        return $allowed && in_array($role, $roles, true);
    });
    $this->actingAs($user);
    $middleware = array_values(array_filter(translationPersistentMiddleware('admin.translation-workbench'), fn ($value) => str_starts_with($value, RoleMiddleware::class.':')));
    expect($middleware)->toHaveCount(1);
    $calls = 0;
    $action = function () use ($middleware, &$calls) {
        return app(Pipeline::class)->send(Request::create('/livewire/update', 'POST'))->through($middleware)->then(function () use (&$calls) {
            $calls++;

            return 'allowed';
        });
    };
    expect($action())->toBe('allowed');
    $allowed = false;
    expect($action)->toThrow(UnauthorizedException::class);
    expect($calls)->toBe(1);
})->with(['Admin', 'Super-Admin']);
