<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('ec_customer_addresses', 'is_out_side_dhaka')) {
            Schema::table('ec_customer_addresses', function (Blueprint $table): void {
                $table->boolean('is_out_side_dhaka')->default(false)->after('inside_dhaka');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('ec_customer_addresses', 'is_out_side_dhaka')) {
            Schema::table('ec_customer_addresses', function (Blueprint $table): void {
                $table->dropColumn('is_out_side_dhaka');
            });
        }
    }
};
