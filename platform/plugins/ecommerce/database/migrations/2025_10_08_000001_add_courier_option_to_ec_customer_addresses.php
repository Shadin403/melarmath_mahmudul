<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('ec_customer_addresses', 'courier_option')) {
            Schema::table('ec_customer_addresses', function (Blueprint $table) {
                $table->enum('courier_option', ['Sundorbon Courier', 'SA Paribahan'])->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('ec_customer_addresses', 'courier_option')) {
            Schema::table('ec_customer_addresses', function (Blueprint $table) {
                $table->dropColumn('courier_option');
            });
        }
    }
};
