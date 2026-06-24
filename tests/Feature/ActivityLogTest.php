<?php

use App\Models\TranslationKey;
use App\Models\User;
use App\Support\Audit\TranslationActivity;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

it('does not persist credentials or password material for failed logins', function (): void {
    event(new Failed('web', null, [
        'email' => 'person@example.test',
        'password' => 'top-secret-password',
        'remember' => true,
    ]));

    $activity = DB::table('activity_log')
        ->where('event', 'login_failed')
        ->first();

    expect($activity)->not->toBeNull();

    $properties = json_decode((string) $activity->properties, true);
    $serializedProperties = json_encode($properties);

    expect($properties)
        ->toBeArray()
        ->and($properties)->not->toHaveKey('credentials')
        ->and($properties['credential_keys'] ?? [])->toBe(['email', 'remember'])
        ->and($properties['login_identifier_hash'] ?? null)->toBe(hash('sha256', 'person@example.test'))
        ->and($serializedProperties)->not->toContain('top-secret-password')
        ->and($serializedProperties)->not->toContain('password');
});

it('records summarized translation admin actions with subject and causer', function (): void {
    $user = User::factory()->create();
    $translationKey = TranslationKey::query()->create([
        'fingerprint' => hash('sha256', 'activity-log-test'),
        'key' => 'admin.activity_log.test',
        'namespace' => 'admin',
        'group' => 'activity_log',
        'status' => 'ok',
        'workflow_status' => 'open',
        'classification' => 'key',
        'source' => 'test',
        'first_seen_at' => now(),
        'last_seen_at' => now(),
    ]);

    $this->actingAs($user);

    app(TranslationActivity::class)->record(
        event: 'translations.admin.key.tested',
        description: 'Translation activity tested',
        subject: $translationKey,
        before: ['workflow_status' => 'open'],
        after: ['workflow_status' => 'reviewed'],
    );

    $activity = DB::table('activity_log')
        ->where('event', 'translations.admin.key.tested')
        ->first();

    expect($activity)
        ->not->toBeNull()
        ->and((int) $activity->subject_id)->toBe($translationKey->id)
        ->and($activity->subject_type)->toBe($translationKey->getMorphClass())
        ->and((int) $activity->causer_id)->toBe($user->id)
        ->and($activity->causer_type)->toBe($user->getMorphClass());

    $properties = json_decode((string) $activity->properties, true);

    expect($properties['before']['workflow_status'] ?? null)->toBe('open')
        ->and($properties['after']['workflow_status'] ?? null)->toBe('reviewed');
});

it('accepts terminal actor context without requiring a model subject', function (): void {
    $originalStoragePath = $this->app->storagePath();
    $testStoragePath = base_path('storage/framework/testing/activity-log-audit');

    File::deleteDirectory($testStoragePath);
    $this->app->useStoragePath($testStoragePath);

    DB::table('activity_log')->insert([
        'log_name' => 'project',
        'description' => 'Console command completed',
        'event' => 'project.test.completed',
        'properties' => json_encode([
            'command' => 'project:test',
            'actor' => ['type' => 'terminal', 'terminal_user' => 'tester'],
        ]),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->artisan('activity-log:audit')->assertSuccessful();

    $runtimeAudit = json_decode(
        (string) File::get($testStoragePath.'/audits/activity-log-runtime-missing.json'),
        true,
    );
    $sourceAudit = json_decode(
        (string) File::get($testStoragePath.'/audits/activity-log-source-usage.json'),
        true,
    );
    $group = collect($runtimeAudit['items'] ?? [])
        ->firstWhere('event', 'project.test.completed');

    expect($group)
        ->toBeArray()
        ->and($group['subject_required'] ?? true)->toBeFalse()
        ->and($group['missing_required_subject'] ?? 1)->toBe(0)
        ->and($group['missing_causer_or_actor'] ?? 1)->toBe(0)
        ->and($sourceAudit['problem_usages'] ?? 1)->toBe(0)
        ->and($sourceAudit['unlogged_mutation_candidate_count'] ?? 1)->toBe(0);

    $this->app->useStoragePath($originalStoragePath);
    File::deleteDirectory($testStoragePath);
});
