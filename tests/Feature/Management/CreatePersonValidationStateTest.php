<?php

use App\Livewire\Management\People\CreatePerson;
use App\Models\AddressLocality;
use App\Models\Country;
use App\Models\Language;
use App\Models\PersonLanguage;
use Livewire\Livewire;

test('existing validation errors clear when their field becomes valid', function (): void {
    Livewire::test(CreatePerson::class)
        ->call('create')
        ->assertHasErrors(['salutation' => 'required'])
        ->set('salutation', 'mr')
        ->assertHasNoErrors('salutation');
});

test('validation toast errors follow configured form field order', function (): void {
    Livewire::test(CreatePerson::class)
        ->set('salutation', 'mr')
        ->set('gender', 'male')
        ->call('create')
        ->assertDispatched('buergerfrs:validation-errors', function (string $event, array $params): bool {
            return ($params['errors'][0]['field'] ?? null) === 'firstName'
                && ($params['errors'][0]['inputId'] ?? null) === 'create-person-first-name'
                && ($params['errors'][0]['tab'] ?? null) === 'person';
        });
});

test('created birth place clears its existing validation error', function (): void {
    $country = Country::query()->create([
        'iso2' => 'DE',
        'iso3' => 'DEU',
        'name' => 'Germany',
        'native_name' => 'Deutschland',
        'is_active' => true,
    ]);

    Livewire::test(CreatePerson::class)
        ->set('birthCountryId', $country->id)
        ->call('create')
        ->assertHasErrors(['birthPlaceText' => 'required'])
        ->call('useCreatedBirthPlaceText', 'Berlin')
        ->assertSet('birthPlaceText', 'Berlin')
        ->assertHasNoErrors('birthPlaceText');
});

test('birth place requires a selected birth country before accepting created values', function (): void {
    Livewire::test(CreatePerson::class)
        ->call('useCreatedBirthPlaceText', 'Berlin')
        ->assertSet('birthPlaceText', '');
});

test('birth place options are loaded from localities for the selected birth country', function (): void {
    $germany = Country::query()->create([
        'iso2' => 'DE',
        'iso3' => 'DEU',
        'name' => 'Germany',
        'native_name' => 'Deutschland',
        'is_active' => true,
    ]);

    $france = Country::query()->create([
        'iso2' => 'FR',
        'iso3' => 'FRA',
        'name' => 'France',
        'native_name' => 'France',
        'is_active' => true,
    ]);

    AddressLocality::query()->create([
        'country_id' => $germany->id,
        'name' => 'Berlin',
        'normalized_name' => 'berlin',
        'is_verified' => true,
        'source' => 'test',
    ]);

    AddressLocality::query()->create([
        'country_id' => $france->id,
        'name' => 'Paris',
        'normalized_name' => 'paris',
        'is_verified' => true,
        'source' => 'test',
    ]);

    Livewire::test(CreatePerson::class)
        ->set('birthCountryId', $germany->id)
        ->assertSee('Berlin')
        ->assertDontSee('Paris');
});

test('create person stores language speaking reading and writing abilities', function (): void {
    $country = Country::query()->create([
        'iso2' => 'DE',
        'iso3' => 'DEU',
        'name' => 'Germany',
        'native_name' => 'Deutschland',
        'is_active' => true,
    ]);
    $language = Language::query()->create([
        'iso639_1' => 'de',
        'iso639_3' => 'deu',
        'name' => 'German',
        'native_name' => 'Deutsch',
        'is_active' => true,
    ]);

    Livewire::test(CreatePerson::class)
        ->set('salutation', 'mr')
        ->set('gender', 'male')
        ->set('maritalStatus', 'single')
        ->set('firstName', 'Language')
        ->set('lastName', 'Person')
        ->set('dateOfBirth', '1990-01-01')
        ->set('birthCountryId', $country->id)
        ->set('birthPlaceText', 'Berlin')
        ->set('email', 'language.person@example.test')
        ->set('addressCountryId', $country->id)
        ->set('addressPostalCode', '10115')
        ->set('addressCity', 'Berlin')
        ->set('addressStreet', 'Invalidenstrasse')
        ->set('addressHouseNumber', '12')
        ->set('primaryNationalityCountryId', [$country->id])
        ->set('primaryLanguageId', [$language->id])
        ->set("languageAbilities.{$language->id}.speaking", true)
        ->set("languageAbilities.{$language->id}.reading", true)
        ->set("languageAbilities.{$language->id}.writing", false)
        ->set('nationalIdNumber', 'TEST-ID-123')
        ->call('create')
        ->assertHasNoErrors();

    $personLanguage = PersonLanguage::query()
        ->where('language_id', $language->id)
        ->first();

    expect($personLanguage)->not->toBeNull()
        ->and($personLanguage?->can_speak)->toBeTrue()
        ->and($personLanguage?->can_read)->toBeTrue()
        ->and($personLanguage?->can_write)->toBeFalse()
        ->and($personLanguage?->preferred_for_communication)->toBeTrue();
});
