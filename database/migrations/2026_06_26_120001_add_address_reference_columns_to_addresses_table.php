<?php

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
        Schema::table('addresses', function (Blueprint $table): void {
            $table->foreignId('postal_code_id')
                ->nullable()
                ->after('country_id')
                ->constrained('address_postal_codes')
                ->nullOnDelete();

            $table->foreignId('locality_id')
                ->nullable()
                ->after('postal_code_id')
                ->constrained('address_localities')
                ->nullOnDelete();

            $table->foreignId('street_id')
                ->nullable()
                ->after('locality_id')
                ->constrained('address_streets')
                ->nullOnDelete();

            $table->index(['country_id', 'postal_code_id', 'locality_id', 'street_id'], 'addresses_reference_scope_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table): void {
            $table->dropIndex('addresses_reference_scope_index');
            $table->dropConstrainedForeignId('street_id');
            $table->dropConstrainedForeignId('locality_id');
            $table->dropConstrainedForeignId('postal_code_id');
        });
    }
};
