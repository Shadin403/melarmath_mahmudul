<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('ec_invoice_items', 'piece')) {
            Schema::table('ec_invoice_items', function (Blueprint $table): void {
                $table->decimal('piece', 15)->after('options')->default(0);
            });
        }

        if (!Schema::hasColumn('ec_invoice_items', 'product_sub_title')) {
            Schema::table('ec_invoice_items', function (Blueprint $table): void {
                $table->text('product_sub_title')->after('piece')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('ec_invoice_items', 'piece')) {
            Schema::table('ec_invoice_items', function (Blueprint $table): void {
                $table->dropColumn('piece');
            });
        }

        if (Schema::hasColumn('ec_invoice_items', 'product_sub_title')) {
            Schema::table('ec_invoice_items', function (Blueprint $table): void {
                $table->dropColumn('product_sub_title');
            });
        }
    }
};
