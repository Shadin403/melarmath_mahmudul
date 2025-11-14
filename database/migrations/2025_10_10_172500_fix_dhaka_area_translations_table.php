<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure the translations table has the correct structure and constraints
        Schema::table('ec_dhaka_area_translations', function (Blueprint $table) {
            // Check if the primary key constraint already exists
            $existingPrimaryKey = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'ec_dhaka_area_translations'
                AND CONSTRAINT_TYPE = 'PRIMARY KEY'
            ");

            // If there's already a primary key, drop it first
            if (!empty($existingPrimaryKey)) {
                $constraintName = $existingPrimaryKey[0]->CONSTRAINT_NAME;
                $table->dropPrimary($constraintName);
            }

            // Make sure the composite primary key is properly set
            $table->primary(['lang_code', 'ec_dhaka_area_id'], 'ec_dhaka_area_translations_primary');

            // Ensure foreign key constraint exists
            if (!Schema::hasColumn('ec_dhaka_area_translations', 'ec_dhaka_area_id')) {
                $table->foreign('ec_dhaka_area_id')
                    ->references('id')
                    ->on('ec_dhaka_area')
                    ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ec_dhaka_area_translations', function (Blueprint $table) {
            // Drop the primary key constraint
            $table->dropPrimary('ec_dhaka_area_translations_primary');
        });
    }
};
