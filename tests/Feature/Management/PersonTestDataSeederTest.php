<?php

use App\Models\Person;
use App\Models\AddressLocality;
use Database\Seeders\CountrySeeder;
use Database\Seeders\PersonTestDataSeeder;
use Illuminate\Support\Facades\DB;

test('person test data seeder creates 300 flagged test people', function (): void {
    $this->seed(PersonTestDataSeeder::class);

    expect(Person::query()->where('is_test_data', true)->count())->toBe(300)
        ->and(Person::query()->where('is_test_data', false)->count())->toBe(0)
        ->and(Person::query()->where('person_number', 'TEST-PER-0001')->value('is_test_data'))->toBeTrue()
        ->and(Person::query()->where('person_number', 'TEST-PER-0300')->value('is_test_data'))->toBeTrue();
});

test('person test data seeder can be run repeatedly without duplicates', function (): void {
    $this->seed(PersonTestDataSeeder::class);
    $this->seed(PersonTestDataSeeder::class);

    expect(Person::query()->where('is_test_data', true)->count())->toBe(300);
});

test('person test data seeder also prepares birth place option references', function (): void {
    $this->seed(CountrySeeder::class);
    $this->seed(PersonTestDataSeeder::class);

    expect(AddressLocality::query()->where('source', 'test-seeder')->count())->toBeGreaterThan(0)
        ->and(Person::query()
            ->where('is_test_data', true)
            ->whereIn('birth_place_text', AddressLocality::query()->pluck('name'))
            ->exists())->toBeTrue();
});

test('person test data seeder writes an activity log entry', function (): void {
    $this->seed(CountrySeeder::class);
    $this->seed(PersonTestDataSeeder::class);

    $activity = DB::table('activity_log')
        ->where('log_name', 'project')
        ->where('event', 'person_test_data.seeded')
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity->description)->toBe('Person test data seeder completed');

    $properties = json_decode((string) $activity->properties, true);

    expect($properties['seeder'] ?? null)->toBe(PersonTestDataSeeder::class)
        ->and($properties['target_people_count'] ?? null)->toBe(300)
        ->and($properties['test_people_count'] ?? null)->toBe(300)
        ->and($properties['actor']['type'] ?? null)->toBe('terminal')
        ->and($properties['address_reference_counts']['localities'] ?? 0)->toBeGreaterThan(0);
});
