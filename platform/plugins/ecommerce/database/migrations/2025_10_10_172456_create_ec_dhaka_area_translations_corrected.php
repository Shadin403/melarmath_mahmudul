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
        Schema::create('ec_dhaka_area_translations', function (Blueprint $table) {
            $table->string('lang_code');
            $table->integer('ec_dhaka_area_id'); // Changed to match the exact type of the referenced column
            $table->string('name')->nullable();

            $table->primary(['lang_code', 'ec_dhaka_area_id'], 'ec_dhaka_area_translations_primary');

            // Create the foreign key constraint with explicit column type matching
            $table->foreign('ec_dhaka_area_id')
                ->references('id')
                ->on('ec_dhaka_area')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ec_dhaka_area_translations');
    }
};
