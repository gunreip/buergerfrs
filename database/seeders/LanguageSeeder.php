<?php

// database/seeders/LanguageSeeder.php

// php artisan db:seed --class=LanguageSeeder

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            [
                'iso639_1' => 'de',
                'iso639_3' => 'deu',
                'name' => 'German',
                'native_name' => 'Deutsch',
                'sort_order' => 10,
            ],
            [
                'iso639_1' => 'en',
                'iso639_3' => 'eng',
                'name' => 'English',
                'native_name' => 'English',
                'sort_order' => 20,
            ],
            [
                'iso639_1' => 'es',
                'iso639_3' => 'spa',
                'name' => 'Spanish',
                'native_name' => 'Español',
                'sort_order' => 30,
            ],
            [
                'iso639_1' => 'fr',
                'iso639_3' => 'fra',
                'name' => 'French',
                'native_name' => 'Français',
                'sort_order' => 40,
            ],
            [
                'iso639_1' => 'it',
                'iso639_3' => 'ita',
                'name' => 'Italian',
                'native_name' => 'Italiano',
                'sort_order' => 50,
            ],
            [
                'iso639_1' => 'nl',
                'iso639_3' => 'nld',
                'name' => 'Dutch',
                'native_name' => 'Nederlands',
                'sort_order' => 60,
            ],
        ];

        foreach ($languages as $language) {
            Language::query()->updateOrCreate(
                ['iso639_3' => $language['iso639_3']],
                array_merge($language, ['is_active' => true]),
            );
        }
    }
}
