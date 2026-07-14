<?php

use App\Livewire\Management\People\CreatePerson;
use App\Models\Person;
use Database\Seeders\AddressReferenceTestDataSeeder;
use Database\Seeders\CountrySeeder;
use Database\Seeders\LanguageSeeder;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('checking test data fills required create person fields', function (): void {
    $this->seed(CountrySeeder::class);
    $this->seed(LanguageSeeder::class);
    $this->seed(AddressReferenceTestDataSeeder::class);

    Livewire::test(CreatePerson::class)
        ->set('isTestData', true)
        ->assertSet('isTestData', true)
        ->assertSet('birthPlaceText', fn (string $value): bool => $value !== '')
        ->assertSet('email', fn (string $value): bool => str_ends_with($value, '@example.test'))
        ->assertSet('addressPostalCode', fn (string $value): bool => $value !== '')
        ->assertSet('addressCity', fn (string $value): bool => $value !== '')
        ->assertSet('addressStreet', fn (string $value): bool => $value !== '')
        ->assertSet('addressHouseNumber', fn (string $value): bool => $value !== '')
        ->assertSet('nationalIdNumber', fn (string $value): bool => str_starts_with($value, 'TEST-ID-'))
        ->assertSet('primaryNationalityCountryId', fn (array $value): bool => $value !== [])
        ->assertSet('primaryLanguageId', fn (array $value): bool => $value !== [])
        ->assertSet('languageAbilities', fn (array $value): bool => $value !== []);
});

test('autofilled test data can be saved as a test person', function (): void {
    Storage::fake('local');

    $this->seed(CountrySeeder::class);
    $this->seed(LanguageSeeder::class);
    $this->seed(AddressReferenceTestDataSeeder::class);

    Livewire::test(CreatePerson::class)
        ->set('isTestData', true)
        ->call('create')
        ->assertHasNoErrors();

    expect(Person::query()->where('is_test_data', true)->count())->toBe(1);
});

test('successful create keeps the entered form data visible', function (): void {
    Storage::fake('local');

    $this->seed(CountrySeeder::class);
    $this->seed(LanguageSeeder::class);
    $this->seed(AddressReferenceTestDataSeeder::class);

    $component = Livewire::test(CreatePerson::class)
        ->set('isTestData', true);

    $firstName = $component->get('firstName');
    $lastName = $component->get('lastName');
    $email = $component->get('email');

    $component
        ->call('create')
        ->assertHasNoErrors()
        ->assertSet('isTestData', true)
        ->assertSet('firstName', $firstName)
        ->assertSet('lastName', $lastName)
        ->assertSet('email', $email)
        ->assertSet('createdPersonId', fn (?int $value): bool => $value !== null)
        ->assertSet('generatedPassword', fn (string $value): bool => $value !== '');
});
