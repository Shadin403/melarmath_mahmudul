<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('ec_product_variations_translations')) {
            Schema::create('ec_product_variations_translations', function (Blueprint $table): void {
                $table->string('lang_code', 20);
                $table->foreignId('ec_product_variations_id');
                $table->string('variation_title')->nullable();
                $table->text('variation_desc')->nullable();

                $table->primary(['lang_code', 'ec_product_variations_id'], 'product_variation_translations_primary');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ec_product_variations_translations');
    }
};
