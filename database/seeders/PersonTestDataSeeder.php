<?php

// php artisan db:seed --class=PersonTestDataSeeder

namespace Database\Seeders;

use App\Models\AddressLocality;
use App\Models\Country;
use App\Models\Person;
use App\Support\ActivityLog\ConsoleActivityContext;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

class PersonTestDataSeeder extends Seeder
{
    private const COUNT = 300;

    private const PERSON_NUMBER_PREFIX = 'TEST-PER-';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! Schema::hasColumn('people', 'is_test_data')) {
            throw new RuntimeException('The people.is_test_data column is missing. Run [php artisan migrate] before running this seeder.');
        }

        $this->call(AddressReferenceTestDataSeeder::class);

        $faker = fake('de_DE');
        $faker->seed(20260627);

        $countries = Country::query()
            ->active()
            ->ordered()
            ->get(['id', 'iso2']);

        $localitiesByCountry = AddressLocality::query()
            ->select(['country_id', 'name'])
            ->when($countries->isNotEmpty(), fn ($query) => $query->whereIn('country_id', $countries->pluck('id')))
            ->ordered()
            ->get()
            ->groupBy('country_id')
            ->map(fn (Collection $localities) => $localities->pluck('name')->unique()->values());

        DB::transaction(function () use ($faker, $countries, $localitiesByCountry): void {
            for ($index = 1; $index <= self::COUNT; $index++) {
                $personNumber = self::PERSON_NUMBER_PREFIX.str_pad((string) $index, 4, '0', STR_PAD_LEFT);
                $person = Person::query()->where('person_number', $personNumber)->first();

                if ($person !== null && ! $person->is_test_data) {
                    throw new RuntimeException("Person number [{$personNumber}] is already used by a non-test person.");
                }

                $gender = $faker->randomElement(['female', 'male', 'diverse', 'unknown']);
                $country = $countries->isNotEmpty() ? $countries->random() : null;

                ($person ?? new Person(['person_number' => $personNumber]))->fill([
                    'is_test_data' => true,
                    'salutation' => $this->salutationForGender($gender),
                    'name_title' => $faker->boolean(8) ? 'Dr.' : null,
                    'gender' => $gender,
                    'marital_status' => $faker->randomElement([
                        'single',
                        'married',
                        'registered_partnership',
                        'divorced',
                        'widowed',
                        'separated',
                        'unknown',
                    ]),
                    'first_name' => $this->firstNameForGender($faker, $gender),
                    'middle_name' => $faker->boolean(18) ? $faker->firstName() : null,
                    'preferred_name' => $faker->boolean(12) ? $faker->firstName() : null,
                    'last_name' => $faker->lastName(),
                    'birth_name' => $faker->boolean(20) ? $faker->lastName() : null,
                    'date_of_birth' => $faker->dateTimeBetween('-82 years', '-18 years')->format('Y-m-d'),
                    'birth_country_id' => $country?->id,
                    'birth_place_text' => $this->birthPlaceForCountry($faker, $country?->id, $localitiesByCountry),
                    'phone' => $faker->boolean(65) ? $faker->phoneNumber() : null,
                    'mobile' => $faker->boolean(85) ? $faker->phoneNumber() : null,
                    'email_private' => "test.person.{$index}@example.test",
                    'email_work' => $faker->boolean(35) ? "test.person.{$index}@work.example.test" : null,
                ])->save();
            }
        });

        $this->logSeedActivity();
    }

    private function salutationForGender(string $gender): string
    {
        return match ($gender) {
            'female' => 'mrs',
            'male' => 'mr',
            default => 'mx',
        };
    }

    private function firstNameForGender(Generator $faker, string $gender): string
    {
        return match ($gender) {
            'female' => $faker->firstNameFemale(),
            'male' => $faker->firstNameMale(),
            default => $faker->firstName(),
        };
    }

    /**
     * @param  Collection<int, Collection<int, string>>  $localitiesByCountry
     */
    private function birthPlaceForCountry(Generator $faker, ?int $countryId, Collection $localitiesByCountry): string
    {
        $localities = $countryId === null ? collect() : $localitiesByCountry->get($countryId, collect());

        if ($localities->isNotEmpty()) {
            return $localities->random();
        }

        return $faker->city();
    }

    private function logSeedActivity(): void
    {
        try {
            activity('project')
                ->event('person_test_data.seeded')
                ->withProperties([
                    'seeder' => self::class,
                    'target_people_count' => self::COUNT,
                    'test_people_count' => Person::query()->where('is_test_data', true)->count(),
                    'address_reference_counts' => [
                        'postal_codes' => DB::table('address_postal_codes')->where('source', 'test-seeder')->count(),
                        'localities' => DB::table('address_localities')->where('source', 'test-seeder')->count(),
                        'streets' => DB::table('address_streets')->where('source', 'test-seeder')->count(),
                    ],
                    'actor' => ConsoleActivityContext::actor(),
                ])
                ->log('Person test data seeder completed');
        } catch (Throwable) {
            //
        }
    }
}
