<?php

use App\Livewire\Admin\TranslationList;
use App\Models\TranslationKey;
use Livewire\Livewire;

function createTranslationKeys(int $count): void
{
    for ($i = 1; $i <= $count; $i++) {
        TranslationKey::query()->create([
            'fingerprint' => hash('sha256', 'translation-key-' . $i),
            'key' => 'tests.translation.' . $i,
            'namespace' => 'messages',
            'group' => 'default',
            'status' => 'ok',
            'classification' => 'native',
            'source' => 'audit',
            'native_text' => 'Native text ' . $i,
            'first_seen_at' => now()->subMinute(),
            'last_seen_at' => now()->addSeconds($i),
            'created_at' => now()->addSeconds($i),
            'updated_at' => now()->addSeconds($i),
        ]);
    }
}

test('goToPage navigates translation pagination', function () {
    createTranslationKeys(30);

    Livewire::test(TranslationList::class)
        ->set('perPage', 10)
        ->call('gotoPage', 2)
        ->assertSee('tests.translation.20')
        ->assertDontSee('tests.translation.30');
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
