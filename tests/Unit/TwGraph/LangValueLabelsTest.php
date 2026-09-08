<?php

use App\Settings\AppGeneralSettings;
use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\LangValueLabels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

uses(TestCase::class);

it('returns no labels when the main row has no lang value ids', function (): void {
    expect(LangValueLabels::active([
        'lang_value_ids' => [],
    ]))->toBe([]);
});

it('returns no labels when lang value ids exist but the table is unavailable', function (): void {
    Schema::shouldReceive('hasTable')
        ->with('translation_workbench_lang_values')
        ->once()
        ->andReturnFalse();

    expect(LangValueLabels::active([
        'lang_value_ids' => [801, 802],
    ]))->toBe([]);
});

it('returns no labels for invalid lang value ids without touching the database', function (): void {
    Schema::shouldReceive('hasTable')->never();

    expect(LangValueLabels::active([
        'lang_value_ids' => [0, -2, null, 'abc'],
    ]))->toBe([]);
});

it('builds source and active target lang value labels from available rows', function (): void {
    $settings = (new ReflectionClass(AppGeneralSettings::class))->newInstanceWithoutConstructor();
    $settings->locale = 'de-DE';

    app()->instance(AppGeneralSettings::class, $settings);
    config()->set('translation-workbench.source_locale', 'en');

    Schema::shouldReceive('hasTable')
        ->with('translation_workbench_lang_values')
        ->once()
        ->andReturnTrue();

    $query = new class {
        public function whereIn(string $column, array $values): self
        {
            if ($column === 'id') {
                expect($values)->toBe([801, 802, 803]);
            }

            if ($column === 'locale') {
                expect($values)->toBe(['en', 'de']);
            }

            return $this;
        }

        public function where(string $column, string $value): self
        {
            expect($column)->toBe('status');
            expect($value)->toBe('active');

            return $this;
        }

        public function get(array $columns)
        {
            expect($columns)->toBe(['id', 'locale', 'locale_role', 'value', 'last_seen_at', 'updated_at', 'created_at']);

            return collect([
                (object) [
                    'id' => 801,
                    'locale' => 'en',
                    'locale_role' => 'source_main',
                    'value' => 'Save',
                    'last_seen_at' => '2026-08-04 09:22:55',
                    'updated_at' => null,
                    'created_at' => null,
                ],
                (object) [
                    'id' => 802,
                    'locale' => 'de',
                    'locale_role' => 'target_main',
                    'value' => 'Speichern',
                    'last_seen_at' => null,
                    'updated_at' => '2026-08-05 10:11:00',
                    'created_at' => null,
                ],
            ]);
        }
    };

    DB::shouldReceive('table')
        ->with('translation_workbench_lang_values')
        ->once()
        ->andReturn($query);

    expect(LangValueLabels::active([
        'lang_value_ids' => [801, '802', 803, 'foo'],
    ]))->toBe([
        'left' => [
            'source lang value ID #801',
            '2026-08-04 09:22 · en · Save',
        ],
        'right' => [
            'target lang value ID #802',
            '2026-08-05 10:11 · de · Speichern',
        ],
    ]);
});
