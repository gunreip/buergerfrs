<?php

use App\Models\TranslationKey;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

beforeEach(function (): void {
    $this->originalStoragePath = $this->app->storagePath();

    $this->testStoragePath = base_path('storage/framework/testing/sync-translation-audits-' . Str::uuid());

    $this->app->useStoragePath($this->testStoragePath);

    File::ensureDirectoryExists(storage_path('audits/translations/compare'));
    File::ensureDirectoryExists(storage_path('audits/translations/lang'));

    File::put(storage_path('audits/translations/compare/ok.json'), json_encode([
        [
            'full_key' => 'foo.bar',
            'usages' => [],
        ],
    ], JSON_PRETTY_PRINT));

    File::put(storage_path('audits/translations/compare/missing.json'), json_encode([], JSON_PRETTY_PRINT));
    File::put(storage_path('audits/translations/compare/obsolete.json'), json_encode([], JSON_PRETTY_PRINT));
    File::put(storage_path('audits/translations/compare/native.json'), json_encode([], JSON_PRETTY_PRINT));
    File::put(storage_path('audits/translations/compare/dynamic.json'), json_encode([], JSON_PRETTY_PRINT));
    File::put(storage_path('audits/translations/compare/invalid.json'), json_encode([
        'code' => [],
        'lang' => [],
    ], JSON_PRETTY_PRINT));

    File::put(storage_path('audits/translations/lang/locales.json'), json_encode([
        ['locale' => 'de'],
        ['locale' => 'en'],
    ], JSON_PRETTY_PRINT));
    File::put(storage_path('audits/translations/lang/keys.json'), json_encode([], JSON_PRETTY_PRINT));
});

afterEach(function (): void {
    $this->app->useStoragePath($this->originalStoragePath);

    if (isset($this->testStoragePath) && is_string($this->testStoragePath) && File::isDirectory($this->testStoragePath)) {
        File::deleteDirectory($this->testStoragePath);
    }
});

it('does not include partials in newly suggested native keys from component partial paths', function (): void {
    $this->artisan('translations:audit-code')
        ->assertSuccessful();

    $nativeEntries = json_decode((string) File::get(storage_path('audits/translations/code/native.json')), true);

    expect($nativeEntries)->toBeArray();

    $changeRoleEntry = collect($nativeEntries)->first(function (array $entry): bool {
        return ($entry['file'] ?? null) === 'resources/views/components/admin/partials/user-list/⚡modal.blade.php'
            && ($entry['value'] ?? null) === 'Change Role';
    });

    expect($changeRoleEntry)->not->toBeNull()
        ->and($changeRoleEntry['suggested_key'] ?? null)->toBe('admin.user_list.modal.change_role');
});

it('normalizes legacy non-key obsolete rows but still marks stale key rows as obsolete', function (): void {
    $legacyNativeObsolete = TranslationKey::query()->create([
        'fingerprint' => hash('sha256', 'legacy-native'),
        'key' => null,
        'namespace' => 'admin',
        'group' => 'partials',
        'status' => 'obsolete',
        'workflow_status' => 'open',
        'classification' => 'native',
        'source' => 'audit',
        'suggested_key' => 'admin.partials.legacy',
        'native_text' => 'Legacy native text',
        'first_seen_at' => now()->subDays(2),
        'last_seen_at' => now()->subDays(1),
        'obsolete_at' => now()->subHours(12),
    ]);

    $staleKey = TranslationKey::query()->create([
        'fingerprint' => hash('sha256', 'stale-key'),
        'key' => 'legacy.never_seen_again',
        'namespace' => 'legacy',
        'group' => 'never_seen_again',
        'status' => 'ok',
        'workflow_status' => 'open',
        'classification' => 'key',
        'source' => 'audit',
        'suggested_key' => 'legacy.never_seen_again',
        'first_seen_at' => now()->subDays(2),
        'last_seen_at' => now()->subDay(),
        'obsolete_at' => null,
    ]);

    $this->artisan('translations:sync-audits')
        ->assertSuccessful();

    $legacyNativeObsolete->refresh();
    $staleKey->refresh();

    expect($legacyNativeObsolete->status)->toBe('native')
        ->and($legacyNativeObsolete->obsolete_at)->toBeNull()
        ->and($legacyNativeObsolete->workflow_status)->toBe('open');

    expect($staleKey->status)->toBe('obsolete')
        ->and($staleKey->obsolete_at)->not->toBeNull();

    expect(TranslationKey::query()
        ->where('status', 'obsolete')
        ->where('classification', 'native')
        ->count())->toBe(0);
});
