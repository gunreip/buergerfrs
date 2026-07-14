<?php

use App\Models\PersonDocument;
use App\Models\PersonDocumentType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('person_document_types', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('label');
            $table->string('category')->default(PersonDocument::CATEGORY_OTHER);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('category');
            $table->index('is_active');
            $table->index('sort_order');
        });

        $now = now();

        foreach (PersonDocumentType::defaults() as $code => $type) {
            DB::table('person_document_types')->insert([
                'code' => $code,
                'label' => $type['label'],
                'category' => $type['category'],
                'is_active' => true,
                'sort_order' => $type['sort_order'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (! Schema::hasTable('person_documents')) {
            return;
        }

        $knownCodes = array_keys(PersonDocumentType::defaults());

        DB::table('person_documents')
            ->whereNotNull('type')
            ->where('type', '!=', '')
            ->distinct()
            ->pluck('type')
            ->each(function (string $code) use ($knownCodes, $now): void {
                if (in_array($code, $knownCodes, true)) {
                    return;
                }

                DB::table('person_document_types')->insertOrIgnore([
                    'code' => $code,
                    'label' => Str::of($code)->replace('_', ' ')->headline()->toString(),
                    'category' => PersonDocument::CATEGORY_OTHER,
                    'is_active' => true,
                    'sort_order' => 500,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('person_document_types');
    }
};
