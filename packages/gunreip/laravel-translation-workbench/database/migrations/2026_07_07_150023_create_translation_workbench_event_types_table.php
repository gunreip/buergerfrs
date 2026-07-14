<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_workbench_event_types', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->string('category', 80)->default('system')->index();
            $table->string('severity', 40)->default('info')->index();
            $table->string('icon')->nullable();
            $table->string('color', 40)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::table('translation_workbench_timeline_events', function (Blueprint $table): void {
            $table->foreignId('event_type_id')
                ->nullable()
                ->after('review_id')
                ->constrained('translation_workbench_event_types')
                ->nullOnDelete();

            $table->index(['event_type_id', 'created_at'], 'tw_timeline_event_type_created_index');
        });

        DB::table('translation_workbench_timeline_events')
            ->whereNotNull('event_type')
            ->select('event_type')
            ->distinct()
            ->orderBy('event_type')
            ->chunk(100, function ($rows): void {
                foreach ($rows as $row) {
                    $eventType = trim((string) $row->event_type);

                    if ($eventType === '') {
                        continue;
                    }

                    $eventTypeId = DB::table('translation_workbench_event_types')->insertGetId([
                        'key' => $eventType,
                        'label' => str($eventType)->replace('_', ' ')->title()->toString(),
                        'category' => 'system',
                        'severity' => 'info',
                        'is_active' => true,
                        'meta' => json_encode([
                            'source' => 'migration_backfill',
                            'auto_created' => true,
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    DB::table('translation_workbench_timeline_events')
                        ->where('event_type', $eventType)
                        ->whereNull('event_type_id')
                        ->update(['event_type_id' => $eventTypeId]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('translation_workbench_timeline_events', function (Blueprint $table): void {
            $table->dropIndex('tw_timeline_event_type_created_index');
            $table->dropConstrainedForeignId('event_type_id');
        });

        Schema::dropIfExists('translation_workbench_event_types');
    }
};
