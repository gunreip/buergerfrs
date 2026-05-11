<?php

// database/seeders/CountrySeeder.php

// php artisan db:seed --class=CountrySeeder

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            [
                'iso2' => 'DE',
                'iso3' => 'DEU',
                'name' => 'Germany',
                'native_name' => 'Deutschland',
                'phone_code' => '+49',
                'sort_order' => 10,
            ],
            [
                'iso2' => 'ES',
                'iso3' => 'ESP',
                'name' => 'Spain',
                'native_name' => 'España',
                'phone_code' => '+34',
                'sort_order' => 20,
            ],
            [
                'iso2' => 'GB',
                'iso3' => 'GBR',
                'name' => 'United Kingdom',
                'native_name' => 'United Kingdom',
                'phone_code' => '+44',
                'sort_order' => 30,
            ],
            [
                'iso2' => 'US',
                'iso3' => 'USA',
                'name' => 'United States',
                'native_name' => 'United States',
                'phone_code' => '+1',
                'sort_order' => 40,
            ],
            [
                'iso2' => 'FR',
                'iso3' => 'FRA',
                'name' => 'France',
                'native_name' => 'France',
                'phone_code' => '+33',
                'sort_order' => 50,
            ],
            [
                'iso2' => 'IT',
                'iso3' => 'ITA',
                'name' => 'Italy',
                'native_name' => 'Italia',
                'phone_code' => '+39',
                'sort_order' => 60,
            ],
            [
                'iso2' => 'NL',
                'iso3' => 'NLD',
                'name' => 'Netherlands',
                'native_name' => 'Nederland',
                'phone_code' => '+31',
                'sort_order' => 70,
            ],
            [
                'iso2' => 'BE',
                'iso3' => 'BEL',
                'name' => 'Belgium',
                'native_name' => 'België',
                'phone_code' => '+32',
                'sort_order' => 80,
            ],
            [
                'iso2' => 'AT',
                'iso3' => 'AUT',
                'name' => 'Austria',
                'native_name' => 'Österreich',
                'phone_code' => '+43',
                'sort_order' => 90,
            ],
            [
                'iso2' => 'CH',
                'iso3' => 'CHE',
                'name' => 'Switzerland',
                'native_name' => 'Schweiz',
                'phone_code' => '+41',
                'sort_order' => 100,
            ],
        ];

        foreach ($countries as $country) {
            Country::query()->updateOrCreate(
                ['iso2' => $country['iso2']],
                array_merge($country, ['is_active' => true]),
            );
        }
    }
}
