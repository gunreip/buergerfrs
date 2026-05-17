<?php

use App\Livewire\Admin\TranslationList;
use App\Models\TranslationKey;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

beforeEach(function (): void {
    Schema::create('translation_keys', function (Blueprint $table): void {
        $table->id();
        $table->string('fingerprint', 64)->unique();
        $table->string('key')->nullable();
        $table->string('namespace')->nullable();
        $table->string('group')->nullable();
        $table->string('status', 32);
        $table->string('classification', 32);
        $table->string('source', 32)->default('audit');
        $table->string('suggested_key')->nullable();
        $table->text('native_text')->nullable();
        $table->timestamp('first_seen_at')->nullable();
        $table->timestamp('last_seen_at')->nullable();
        $table->timestamp('obsolete_at')->nullable();
        $table->timestamps();
    });

    Schema::create('translation_values', function (Blueprint $table): void {
        $table->id();
        $table->unsignedBigInteger('translation_key_id');
        $table->string('locale', 20);
        $table->longText('value')->nullable();
        $table->string('status', 32)->default('missing');
        $table->string('source', 32)->default('audit');
        $table->timestamp('reviewed_at')->nullable();
        $table->unsignedBigInteger('reviewed_by_user_id')->nullable();
        $table->timestamps();
    });

    Schema::create('translation_usages', function (Blueprint $table): void {
        $table->id();
        $table->unsignedBigInteger('translation_key_id');
        $table->string('fingerprint', 64)->unique();
        $table->string('file');
        $table->unsignedInteger('line')->nullable();
        $table->string('function', 64)->nullable();
        $table->string('classification', 32);
        $table->string('reason')->nullable();
        $table->text('raw')->nullable();
        $table->timestamps();
    });
});

afterEach(function (): void {
    Schema::dropIfExists('translation_usages');
    Schema::dropIfExists('translation_values');
    Schema::dropIfExists('translation_keys');
});

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
        ->call('goToPage', 2)
        ->assertSee('tests.translation.20')
        ->assertDontSee('tests.translation.30');
});

test('first previous next and last pagination actions are callable', function () {
    createTranslationKeys(30);

    Livewire::test(TranslationList::class)
        ->set('perPage', 10)
        ->call('goToFirstPage')
        ->assertSee('tests.translation.30')
        ->call('goToNextPage')
        ->assertSee('tests.translation.20')
        ->call('goToLastPage')
        ->assertSee('tests.translation.1')
        ->call('goToPreviousPage')
        ->assertSee('tests.translation.11');
});
