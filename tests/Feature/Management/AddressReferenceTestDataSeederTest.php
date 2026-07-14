<?php

use App\Models\AddressLocality;
use App\Models\AddressPostalCode;
use App\Models\AddressStreet;
use Database\Seeders\AddressReferenceTestDataSeeder;
use Database\Seeders\CountrySeeder;

test('address reference test data seeder creates reusable postal code locality and street options', function (): void {
    $this->seed(CountrySeeder::class);
    $this->seed(AddressReferenceTestDataSeeder::class);

    expect(AddressPostalCode::query()->where('source', 'test-seeder')->count())->toBeGreaterThan(0)
        ->and(AddressLocality::query()->where('source', 'test-seeder')->count())->toBeGreaterThan(0)
        ->and(AddressStreet::query()->where('source', 'test-seeder')->count())->toBeGreaterThan(0)
        ->and(AddressLocality::query()->where('name', 'Berlin')->exists())->toBeTrue();
});

test('address reference test data seeder can be run repeatedly without duplicates', function (): void {
    $this->seed(CountrySeeder::class);
    $this->seed(AddressReferenceTestDataSeeder::class);
    $this->seed(AddressReferenceTestDataSeeder::class);

    expect(AddressLocality::query()->where('name', 'Berlin')->count())->toBe(1);
});
