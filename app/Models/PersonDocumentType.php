<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

#[Fillable([
    'code',
    'label',
    'category',
    'is_active',
    'sort_order',
])]
class PersonDocumentType extends Model
{
    /**
     * @return array<string, array{label: string, category: string, sort_order: int}>
     */
    public static function defaults(): array
    {
        return [
            PersonDocument::TYPE_ID_CARD_COPY => [
                'label' => 'ID card copy',
                'category' => PersonDocument::CATEGORY_IDENTITY,
                'sort_order' => 10,
            ],
            PersonDocument::TYPE_PASSPORT_COPY => [
                'label' => 'Passport copy',
                'category' => PersonDocument::CATEGORY_IDENTITY,
                'sort_order' => 20,
            ],
            PersonDocument::TYPE_RESIDENCE_PERMIT_COPY => [
                'label' => 'Residence permit copy',
                'category' => PersonDocument::CATEGORY_RESIDENCE,
                'sort_order' => 30,
            ],
            PersonDocument::TYPE_HEALTH_INSURANCE_PROOF => [
                'label' => 'Health insurance proof',
                'category' => PersonDocument::CATEGORY_INSURANCE,
                'sort_order' => 40,
            ],
            PersonDocument::TYPE_TAX_DOCUMENT => [
                'label' => 'Tax document',
                'category' => PersonDocument::CATEGORY_TAX,
                'sort_order' => 50,
            ],
            'invoice' => [
                'label' => 'Invoice',
                'category' => PersonDocument::CATEGORY_OTHER,
                'sort_order' => 60,
            ],
            'receipt' => [
                'label' => 'Receipt',
                'category' => PersonDocument::CATEGORY_OTHER,
                'sort_order' => 70,
            ],
            PersonDocument::TYPE_OTHER => [
                'label' => 'Other',
                'category' => PersonDocument::CATEGORY_OTHER,
                'sort_order' => 999,
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function defaultOptions(): array
    {
        return collect(self::defaults())
            ->mapWithKeys(fn(array $type, string $code): array => [$code => $type['label']])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        if (! Schema::hasTable('person_document_types')) {
            return self::defaultOptions();
        }

        $options = self::query()
            ->active()
            ->ordered()
            ->pluck('label', 'code')
            ->all();

        return $options !== [] ? $options : self::defaultOptions();
    }

    public static function categoryFor(string $code): string
    {
        if (Schema::hasTable('person_document_types')) {
            $category = self::query()
                ->where('code', $code)
                ->value('category');

            if (is_string($category) && in_array($category, PersonDocument::CATEGORIES, true)) {
                return $category;
            }
        }

        return self::defaults()[$code]['category'] ?? PersonDocument::CATEGORY_OTHER;
    }

    public static function firstOrCreateFromLabel(string $label): ?self
    {
        $label = trim($label);

        if ($label === '') {
            return null;
        }

        $code = self::codeFromLabel($label);

        if ($code === '') {
            return null;
        }

        return self::query()->updateOrCreate(
            ['code' => $code],
            [
                'label' => $label,
                'category' => self::categoryFor($code),
                'is_active' => true,
            ],
        );
    }

    public static function codeFromLabel(string $label): string
    {
        return Str::of(Str::ascii($label, 'de'))
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_')
            ->toString();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('sort_order')
            ->orderBy('label');
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
