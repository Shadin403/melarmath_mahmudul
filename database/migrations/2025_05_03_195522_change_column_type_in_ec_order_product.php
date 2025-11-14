<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ec_order_product', function (Blueprint $table) {
            $table->decimal('qty')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ec_order_product', function (Blueprint $table) {
            $table->integer('qty')->change();//first check the qty column type and then change it to whice you need.[if column type is integer then user default otherwish user second onc]
            // $table->decimal('qty')->change();
        });
    }
};
