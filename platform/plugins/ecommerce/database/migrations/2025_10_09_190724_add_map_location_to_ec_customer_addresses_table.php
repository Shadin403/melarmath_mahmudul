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
        if (! Schema::hasColumn('ec_customer_addresses', 'map_location')) {
            Schema::table('ec_customer_addresses', function (Blueprint $table) {
                $table->text('map_location')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('ec_customer_addresses', 'map_location')) {
            Schema::table('ec_customer_addresses', function (Blueprint $table) {
                $table->dropColumn('map_location');
            });
        }
    }
};
