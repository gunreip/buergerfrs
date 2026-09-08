<?php

declare(strict_types=1);

use App\Settings\AppGeneralSettings;
use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\LocaleResolver;
use Tests\TestCase;

uses(TestCase::class);

it('uses the app settings locale language as active target locale', function (): void {
    $settings = (new ReflectionClass(AppGeneralSettings::class))->newInstanceWithoutConstructor();
    $settings->locale = 'de-DE';

    app()->instance(AppGeneralSettings::class, $settings);

    expect(LocaleResolver::activeTargetMainLocale())->toBe('de');
});

it('falls back to the laravel app locale when settings locale is empty', function (): void {
    $settings = (new ReflectionClass(AppGeneralSettings::class))->newInstanceWithoutConstructor();
    $settings->locale = '';

    app()->instance(AppGeneralSettings::class, $settings);
    app()->setLocale('es_ES');

    expect(LocaleResolver::activeTargetMainLocale())->toBe('es');
});

it('returns the normalized configured locale when it has no separate language part', function (): void {
    $settings = (new ReflectionClass(AppGeneralSettings::class))->newInstanceWithoutConstructor();
    $settings->locale = 'de';

    app()->instance(AppGeneralSettings::class, $settings);

    expect(LocaleResolver::activeTargetMainLocale())->toBe('de');
});
