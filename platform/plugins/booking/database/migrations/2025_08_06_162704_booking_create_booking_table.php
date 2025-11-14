<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->string('status', 60)->default('published');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('bookings_translations')) {
            Schema::create('bookings_translations', function (Blueprint $table) {
                $table->string('lang_code');
                $table->foreignId('bookings_id');
                $table->string('name', 255)->nullable();

                $table->primary(['lang_code', 'bookings_id'], 'bookings_translations_primary');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('bookings_translations');
    }
};
