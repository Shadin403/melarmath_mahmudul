<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mp_stores', function (Blueprint $table) {
            $table->boolean('is_out_side_dhaka')->default(false)->after('address');
            $table->unsignedBigInteger('is_inside_of_dhaka')->nullable()->after('is_out_side_dhaka');
            $table->unsignedBigInteger('inside_dhaka')->nullable()->after('is_inside_of_dhaka');
            $table->text('map_location')->nullable()->after('inside_dhaka');
        });
    }

    public function down(): void
    {
        Schema::table('mp_stores', function (Blueprint $table) {
            $table->dropColumn(['is_out_side_dhaka', 'is_inside_of_dhaka', 'inside_dhaka', 'map_location']);
        });
    }
};
