<?php

// database/migrations/2026_05_08_075227_add_contact_email_to_people_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('people', function (Blueprint $table): void {
            $table->string('email_private')
                ->nullable()
                ->after('mobile');

            $table->string('email_work')
                ->nullable()
                ->after('email_private');

            $table->index('email_private');
            $table->index('email_work');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('people', function (Blueprint $table): void {
            if (Schema::hasColumn('people', 'email_private')) {
                $table->dropColumn('email_private');
            }

            if (Schema::hasColumn('people', 'email_work')) {
                $table->dropColumn('email_work');
            }
        });
    }
};
