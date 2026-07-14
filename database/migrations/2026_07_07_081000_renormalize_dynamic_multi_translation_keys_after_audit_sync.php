<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('translation_keys')
            ->where('is_dynamic_multi', true)
            ->orderBy('id')
            ->chunkById(200, function ($translationKeys): void {
                foreach ($translationKeys as $translationKey) {
                    $key = trim((string) ($translationKey->key ?? ''));
                    $segments = $key !== '' ? explode('.', $key) : [];

                    DB::table('translation_keys')
                        ->where('id', $translationKey->id)
                        ->update([
                            'namespace' => $segments[0] ?? null,
                            'group' => $segments[1] ?? null,
                            'classification' => 'dynamic',
                            'status' => 'dynamic',
                            'source' => 'dynamic_audit',
                            'updated_at' => now(),
                        ]);
                }
            });
    }

    public function down(): void
    {
        //
    }
};
