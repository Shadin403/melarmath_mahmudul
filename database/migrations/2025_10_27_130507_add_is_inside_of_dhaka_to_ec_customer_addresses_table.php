<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ec_customer_addresses', function (Blueprint $table) {
            $table->unsignedInteger('is_inside_of_dhaka')
                ->nullable()
                ->after('is_out_side_dhaka')
                ->comment('Area ID or zone ID inside Dhaka');
        });
    }

    public function down(): void
    {
        Schema::table('ec_customer_addresses', function (Blueprint $table) {
            $table->dropColumn('is_inside_of_dhaka');
        });
    }
};
