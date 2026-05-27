<?php

// database/migrations/2026_05_21_023946_create_translation_languages_table.php

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
        Schema::create('translation_languages', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 32)->unique();
            $table->string('name', 120);
            $table->string('native_name', 120);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_enabled_for_translation')->default(true);
            $table->boolean('is_enabled_for_app')->default(false);
            $table->unsignedInteger('sort_order')->default(100);
            $table->timestamps();

            $table->index('is_enabled_for_translation');
            $table->index('is_enabled_for_app');
            $table->index('sort_order');
        });

        DB::table('translation_languages')->insert([
            [
                'locale' => 'de',
                'name' => 'German',
                'native_name' => 'Deutsch',
                'is_default' => true,
                'is_enabled_for_translation' => true,
                'is_enabled_for_app' => true,
                'sort_order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'locale' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'is_default' => false,
                'is_enabled_for_translation' => true,
                'is_enabled_for_app' => true,
                'sort_order' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_languages');
    }
};
