<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('person_languages', function (Blueprint $table): void {
            $table->boolean('can_speak')->default(false)->after('preferred_for_communication');
            $table->boolean('can_read')->default(false)->after('can_speak');
            $table->boolean('can_write')->default(false)->after('can_read');

            $table->index('can_speak');
            $table->index('can_read');
            $table->index('can_write');
        });

        DB::table('person_languages')
            ->where('preferred_for_communication', true)
            ->update(['can_speak' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('person_languages', function (Blueprint $table): void {
            $table->dropIndex(['can_speak']);
            $table->dropIndex(['can_read']);
            $table->dropIndex(['can_write']);

            $table->dropColumn(['can_speak', 'can_read', 'can_write']);
        });
    }
};
