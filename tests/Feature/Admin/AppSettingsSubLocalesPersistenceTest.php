<?php

use App\Livewire\Admin\AppSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

function createLanguage(string $iso6391, string $name, int $sortOrder = 0): int
{
    return (int) DB::table('languages')->insertGetId([
        'iso639_1' => strtolower($iso6391),
        'iso639_3' => null,
        'iso639_2_b' => null,
        'iso639_2_t' => null,
        'name' => $name,
        'native_name' => $name,
        'scope' => null,
        'type' => null,
        'macrolanguage_code' => null,
        'default_script' => null,
        'is_active' => true,
        'sort_order' => $sortOrder,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

function createLocale(
    string $code,
    int $languageId,
    bool $isActive,
    int $sortOrder = 0,
    ?string $displayName = null,
): void {
    DB::table('locales')->insert([
        'code' => $code,
        'normalized_code' => $code,
        'language_id' => $languageId,
        'country_id' => null,
        'script_code' => null,
        'variant' => null,
        'display_name' => $displayName ?? $code,
        'native_display_name' => $displayName ?? $code,
        'is_active' => $isActive,
        'is_default' => false,
        'sort_order' => $sortOrder,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

test('loads active sub-locales from database for selected primary language', function (): void {
    $deLanguageId = createLanguage('de', 'German');

    createLocale('de-DE', $deLanguageId, true, 0);
    createLocale('de-AT', $deLanguageId, true, 10);
    createLocale('de-LU', $deLanguageId, true, 20);
    createLocale('de-CH', $deLanguageId, false, 30);

    Livewire::test(AppSettings::class)
        ->set('selectedPrimaryLanguageCode', 'de')
        ->assertSet('selectedSubLocaleCodes', ['de-DE', 'de-AT', 'de-LU']);
});

test('toggleSelectedSubLocale persists is_active changes', function (): void {
    $deLanguageId = createLanguage('de', 'German');

    createLocale('de-DE', $deLanguageId, true, 0);
    createLocale('de-AT', $deLanguageId, true, 10);
    createLocale('de-LU', $deLanguageId, false, 20);

    $component = Livewire::test(AppSettings::class)
        ->set('selectedPrimaryLanguageCode', 'de');

    $component
        ->call('toggleSelectedSubLocale', 'de-LU')
        ->assertSet('selectedSubLocaleCodes', ['de-DE', 'de-AT', 'de-LU']);

    assertDatabaseHas('locales', [
        'code' => 'de-LU',
        'is_active' => true,
    ]);

    $component
        ->call('toggleSelectedSubLocale', 'de-AT')
        ->assertSet('selectedSubLocaleCodes', ['de-DE', 'de-LU']);

    assertDatabaseHas('locales', [
        'code' => 'de-AT',
        'is_active' => false,
    ]);
});

test('toggleAllSelectedSubLocales toggles all sub-locales in database', function (): void {
    $deLanguageId = createLanguage('de', 'German');

    createLocale('de-DE', $deLanguageId, true, 0);
    createLocale('de-AT', $deLanguageId, true, 10);
    createLocale('de-LU', $deLanguageId, false, 20);
    createLocale('de-CH', $deLanguageId, false, 30);

    $component = Livewire::test(AppSettings::class)
        ->set('selectedPrimaryLanguageCode', 'de');

    $component
        ->call('toggleAllSelectedSubLocales')
        ->assertSet('selectedSubLocaleCodes', ['de-DE', 'de-AT', 'de-LU', 'de-CH']);

    assertDatabaseHas('locales', ['code' => 'de-DE', 'is_active' => true]);
    assertDatabaseHas('locales', ['code' => 'de-AT', 'is_active' => true]);
    assertDatabaseHas('locales', ['code' => 'de-LU', 'is_active' => true]);
    assertDatabaseHas('locales', ['code' => 'de-CH', 'is_active' => true]);

    $component
        ->call('toggleAllSelectedSubLocales')
        ->assertSet('selectedSubLocaleCodes', []);

    assertDatabaseHas('locales', ['code' => 'de-DE', 'is_active' => false]);
    assertDatabaseHas('locales', ['code' => 'de-AT', 'is_active' => false]);
    assertDatabaseHas('locales', ['code' => 'de-LU', 'is_active' => false]);
    assertDatabaseHas('locales', ['code' => 'de-CH', 'is_active' => false]);
});

test('toggleSelectedSubLocale can toggle the same locale repeatedly without reload', function (): void {
    $deLanguageId = createLanguage('de', 'German');

    createLocale('de-DE', $deLanguageId, true, 0);
    createLocale('de-AT', $deLanguageId, true, 10);
    createLocale('de-LU', $deLanguageId, false, 20);

    $component = Livewire::test(AppSettings::class)
        ->set('selectedPrimaryLanguageCode', 'de');

    $component
        ->call('toggleSelectedSubLocale', 'de-LU')
        ->assertSet('selectedSubLocaleCodes', ['de-DE', 'de-AT', 'de-LU']);

    assertDatabaseHas('locales', ['code' => 'de-LU', 'is_active' => true]);

    $component
        ->call('toggleSelectedSubLocale', 'de-LU')
        ->assertSet('selectedSubLocaleCodes', ['de-DE', 'de-AT']);

    assertDatabaseHas('locales', ['code' => 'de-LU', 'is_active' => false]);

    $component
        ->call('toggleSelectedSubLocale', 'de-LU')
        ->assertSet('selectedSubLocaleCodes', ['de-DE', 'de-AT', 'de-LU']);

    assertDatabaseHas('locales', ['code' => 'de-LU', 'is_active' => true]);
});

test('selectPrimaryLanguage path keeps sub-locale toggle stable without reload', function (): void {
    $deLanguageId = createLanguage('de', 'German');

    createLocale('de-DE', $deLanguageId, true, 0);
    createLocale('de-AT', $deLanguageId, true, 10);
    createLocale('de-LU', $deLanguageId, false, 20);

    $component = Livewire::test(AppSettings::class)
        ->call('selectPrimaryLanguage', 'de')
        ->assertSet('selectedPrimaryLanguageCode', 'de')
        ->assertSet('selectedSubLocaleCodes', ['de-DE', 'de-AT']);

    $component
        ->call('toggleSelectedSubLocale', 'de-LU')
        ->assertSet('selectedSubLocaleCodes', ['de-DE', 'de-AT', 'de-LU']);

    assertDatabaseHas('locales', ['code' => 'de-LU', 'is_active' => true]);

    $component
        ->call('toggleSelectedSubLocale', 'de-LU')
        ->assertSet('selectedSubLocaleCodes', ['de-DE', 'de-AT']);

    assertDatabaseHas('locales', ['code' => 'de-LU', 'is_active' => false]);
});
