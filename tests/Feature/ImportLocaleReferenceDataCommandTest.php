<?php

// tests/Feature/ImportLocaleReferenceDataCommandTest.php

use Illuminate\Support\Facades\Artisan;

test('locale reference import command exposes all regression-critical options', function () {
    $commands = Artisan::all();

    expect($commands)->toHaveKey('reference:import-locale-data');

    $definition = $commands['reference:import-locale-data']->getDefinition();

    expect($definition->hasOption('dry-run'))->toBeTrue();
    expect($definition->hasOption('locales'))->toBeTrue();
    expect($definition->hasOption('with-country-meta'))->toBeTrue();
    expect($definition->hasOption('with-addressing'))->toBeTrue();
    expect($definition->hasOption('with-subdivisions'))->toBeTrue();
});

test('locale reference import command dry-runs with all import blocks enabled', function () {
    $exitCode = Artisan::call('reference:import-locale-data', [
        '--dry-run' => true,
        '--locales' => 'de,en',
        '--with-country-meta' => true,
        '--with-addressing' => true,
        '--with-subdivisions' => true,
    ]);

    $output = Artisan::output();

    expect($exitCode)->toBe(0);
    expect(str_contains($output, 'Locale reference data import finished.'))->toBeTrue();
    expect(str_contains($output, 'Country subdivisions'))->toBeTrue();
});
