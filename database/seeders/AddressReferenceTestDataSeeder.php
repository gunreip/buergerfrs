<?php

// php artisan db:seed --class=AddressReferenceTestDataSeeder

namespace Database\Seeders;

use App\Models\AddressLocality;
use App\Models\AddressPostalCode;
use App\Models\AddressStreet;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class AddressReferenceTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['address_postal_codes', 'address_localities', 'address_streets'] as $table) {
            if (! Schema::hasTable($table)) {
                throw new RuntimeException("The {$table} table is missing. Run [php artisan migrate] before running this seeder.");
            }
        }

        DB::transaction(function (): void {
            foreach ($this->references() as $iso2 => $references) {
                $country = Country::query()->where('iso2', $iso2)->first();

                if ($country === null) {
                    continue;
                }

                foreach ($references as $reference) {
                    $postalCode = AddressPostalCode::query()->firstOrCreate([
                        'country_id' => $country->id,
                        'normalized_postal_code' => $this->normalize($reference['postal_code']),
                    ], [
                        'postal_code' => $reference['postal_code'],
                        'is_verified' => true,
                        'source' => 'test-seeder',
                    ]);

                    $locality = AddressLocality::query()->firstOrCreate([
                        'country_id' => $country->id,
                        'postal_code_id' => $postalCode->id,
                        'normalized_name' => $this->normalize($reference['city']),
                    ], [
                        'name' => $reference['city'],
                        'is_verified' => true,
                        'source' => 'test-seeder',
                    ]);

                    foreach ($reference['streets'] as $street) {
                        AddressStreet::query()->firstOrCreate([
                            'country_id' => $country->id,
                            'postal_code_id' => $postalCode->id,
                            'locality_id' => $locality->id,
                            'normalized_name' => $this->normalize($street),
                        ], [
                            'name' => $street,
                            'is_verified' => true,
                            'source' => 'test-seeder',
                        ]);
                    }
                }
            }
        });
    }

    /**
     * @return array<string, array<int, array{postal_code: string, city: string, streets: array<int, string>}>>
     */
    private function references(): array
    {
        return [
            'DE' => [
                ['postal_code' => '10115', 'city' => 'Berlin', 'streets' => ['Invalidenstrasse', 'Torstrasse', 'Chausseestrasse']],
                ['postal_code' => '20095', 'city' => 'Hamburg', 'streets' => ['Moenckebergstrasse', 'Steinstrasse', 'Ballindamm']],
                ['postal_code' => '80331', 'city' => 'Muenchen', 'streets' => ['Marienplatz', 'Tal', 'Sendlinger Strasse']],
                ['postal_code' => '50667', 'city' => 'Koeln', 'streets' => ['Schildergasse', 'Hohe Strasse', 'Breite Strasse']],
                ['postal_code' => '60311', 'city' => 'Frankfurt am Main', 'streets' => ['Zeil', 'Neue Kraeme', 'Berliner Strasse']],
                ['postal_code' => '70173', 'city' => 'Stuttgart', 'streets' => ['Koenigstrasse', 'Lautenschlagerstrasse', 'Bolzstrasse']],
            ],
            'AT' => [
                ['postal_code' => '1010', 'city' => 'Wien', 'streets' => ['Kaerntner Strasse', 'Graben', 'Rotenturmstrasse']],
                ['postal_code' => '5020', 'city' => 'Salzburg', 'streets' => ['Getreidegasse', 'Linzer Gasse', 'Schwarzstrasse']],
            ],
            'CH' => [
                ['postal_code' => '8001', 'city' => 'Zuerich', 'streets' => ['Bahnhofstrasse', 'Rennweg', 'Limmatquai']],
                ['postal_code' => '3011', 'city' => 'Bern', 'streets' => ['Kramgasse', 'Marktgasse', 'Spitalgasse']],
            ],
            'FR' => [
                ['postal_code' => '75001', 'city' => 'Paris', 'streets' => ['Rue de Rivoli', 'Rue Saint-Honore', 'Avenue de l Opera']],
                ['postal_code' => '69001', 'city' => 'Lyon', 'streets' => ['Rue de la Republique', 'Rue Edouard Herriot', 'Quai Saint-Vincent']],
            ],
            'IT' => [
                ['postal_code' => '00118', 'city' => 'Roma', 'streets' => ['Via Appia Nuova', 'Via Nazionale', 'Via Cavour']],
                ['postal_code' => '20121', 'city' => 'Milano', 'streets' => ['Via Manzoni', 'Via Monte Napoleone', 'Corso Garibaldi']],
            ],
            'ES' => [
                ['postal_code' => '28013', 'city' => 'Madrid', 'streets' => ['Calle Mayor', 'Calle Arenal', 'Gran Via']],
                ['postal_code' => '08002', 'city' => 'Barcelona', 'streets' => ['La Rambla', 'Carrer de Ferran', 'Via Laietana']],
            ],
            'NL' => [
                ['postal_code' => '1012', 'city' => 'Amsterdam', 'streets' => ['Damrak', 'Kalverstraat', 'Rokin']],
                ['postal_code' => '3011', 'city' => 'Rotterdam', 'streets' => ['Coolsingel', 'Meent', 'Witte de Withstraat']],
            ],
            'BE' => [
                ['postal_code' => '1000', 'city' => 'Brussels', 'streets' => ['Rue Neuve', 'Boulevard Anspach', 'Rue Royale']],
                ['postal_code' => '2000', 'city' => 'Antwerp', 'streets' => ['Meir', 'Nationalestraat', 'Kipdorp']],
            ],
            'GB' => [
                ['postal_code' => 'SW1A 1AA', 'city' => 'London', 'streets' => ['Whitehall', 'The Mall', 'Victoria Street']],
                ['postal_code' => 'M1 1AE', 'city' => 'Manchester', 'streets' => ['Market Street', 'Deansgate', 'King Street']],
            ],
            'US' => [
                ['postal_code' => '10001', 'city' => 'New York', 'streets' => ['Broadway', 'Madison Avenue', 'Park Avenue']],
                ['postal_code' => '94102', 'city' => 'San Francisco', 'streets' => ['Market Street', 'Van Ness Avenue', 'Mission Street']],
            ],
        ];
    }

    private function normalize(string $value): string
    {
        return mb_strtolower(trim($value));
    }
}
