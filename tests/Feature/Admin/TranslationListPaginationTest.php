<?php

use App\Livewire\Admin\TranslationList;
use App\Models\TranslationKey;
use App\Models\TranslationUsage;
use Livewire\Livewire;

function createTranslationKeys(int $count): void
{
    for ($i = 1; $i <= $count; $i++) {
        $translationKey = TranslationKey::query()->create([
            'fingerprint' => hash('sha256', 'translation-key-'.$i),
            'key' => 'tests.translation.'.$i,
            'namespace' => 'messages',
            'group' => 'default',
            'status' => 'ok',
            'classification' => 'key',
            'source' => 'audit',
            'native_text' => 'Native text '.$i,
            'first_seen_at' => now()->subMinute(),
            'last_seen_at' => now()->addSeconds($i),
            'created_at' => now()->addSeconds($i),
            'updated_at' => now()->addSeconds($i),
        ]);

        TranslationUsage::query()->create([
            'translation_key_id' => $translationKey->id,
            'fingerprint' => hash('sha256', 'translation-usage-'.$i),
            'file' => 'tests/fixtures/translation-'.$i.'.php',
            'line' => $i,
            'function' => '__',
            'classification' => 'key',
            'reason' => null,
            'raw' => "__('tests.translation.{$i}')",
        ]);
    }
}

test('goToPage navigates translation pagination', function () {
    createTranslationKeys(30);

    Livewire::test(TranslationList::class)
        ->set('perPage', 10)
        ->call('gotoPage', 2)
        ->assertSet('paginators.page', 2)
        ->assertSee('Native text 20')
        ->assertDontSee('Native text 30');
});

test('first previous next and last pagination actions are callable', function () {
    createTranslationKeys(30);

    Livewire::test(TranslationList::class)
        ->set('perPage', 10)
        ->call('setPage', 1)
        ->assertSet('paginators.page', 1)
        ->call('nextPage')
        ->assertSet('paginators.page', 2)
        ->call('setPage', 3)
        ->assertSet('paginators.page', 3)
        ->call('previousPage')
        ->assertSet('paginators.page', 2);
});
