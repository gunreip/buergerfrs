<?php

use App\Livewire\Management\People\PersonOverview;
use App\Models\Client;
use App\Models\ClientPerson;
use App\Models\Country;
use App\Models\Person;
use App\Models\User;
use Livewire\Livewire;

test('authenticated users can open the people overview', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('management.people.index'))
        ->assertOk()
        ->assertSeeLivewire(PersonOverview::class);
});

test('people overview lists person records with core metadata', function (): void {
    $user = User::factory()->create();
    $country = Country::query()->create([
        'iso2' => 'DE',
        'iso3' => 'DEU',
        'name' => 'Germany',
        'native_name' => 'Deutschland',
        'is_active' => true,
    ]);

    Person::query()->create([
        'person_number' => 'P-REAL-001',
        'is_test_data' => false,
        'first_name' => 'Anna',
        'last_name' => 'Muster',
        'date_of_birth' => '1988-04-12',
        'birth_country_id' => $country->id,
        'birth_place_text' => 'Berlin',
        'email_private' => 'anna@example.test',
    ]);

    Livewire::actingAs($user)
        ->test(PersonOverview::class)
        ->assertSee('Anna')
        ->assertSee('Muster')
        ->assertSee('P-REAL-001')
        ->assertSee('Berlin')
        ->assertSee('anna@example.test');
});

test('people overview filters test data and search text', function (): void {
    $user = User::factory()->create();

    Person::query()->create([
        'person_number' => 'TEST-PER-9001',
        'is_test_data' => true,
        'first_name' => 'Testa',
        'last_name' => 'Seedling',
        'birth_place_text' => 'Hamburg',
    ]);

    Person::query()->create([
        'person_number' => 'REAL-PER-9001',
        'is_test_data' => false,
        'first_name' => 'Reala',
        'last_name' => 'Person',
        'birth_place_text' => 'Munich',
    ]);

    Livewire::actingAs($user)
        ->test(PersonOverview::class)
        ->set('testDataFilter', 'test')
        ->assertSee('Seedling')
        ->assertDontSee('Reala')
        ->set('search', 'hamburg')
        ->assertSee('Seedling')
        ->assertDontSee('Reala');
});

test('people overview filters user and client relations', function (): void {
    $viewer = User::factory()->create();
    $linkedPerson = Person::query()->create([
        'person_number' => 'P-LINKED',
        'first_name' => 'Linked',
        'last_name' => 'Person',
    ]);
    $standalonePerson = Person::query()->create([
        'person_number' => 'P-STANDALONE',
        'first_name' => 'Standalone',
        'last_name' => 'Person',
    ]);
    $client = Client::query()->create([
        'client_number' => 'C-001',
        'name' => 'Client One',
        'status' => Client::STATUS_ACTIVE,
    ]);

    User::factory()->create([
        'person_id' => $linkedPerson->id,
        'name' => 'Linked User',
    ]);

    $client->people()->attach($linkedPerson->id, [
        'relationship_type' => ClientPerson::RELATIONSHIP_MEMBER,
        'status' => ClientPerson::STATUS_ACTIVE,
    ]);

    Livewire::actingAs($viewer)
        ->test(PersonOverview::class)
        ->set('userFilter', 'with_user')
        ->assertSee('Linked')
        ->assertDontSee('Standalone')
        ->set('userFilter', '')
        ->set('clientFilter', 'with_client')
        ->assertSee('Linked')
        ->assertDontSee('Standalone')
        ->set('clientFilter', 'without_client')
        ->assertSee('Standalone')
        ->assertDontSee('Linked');
});
