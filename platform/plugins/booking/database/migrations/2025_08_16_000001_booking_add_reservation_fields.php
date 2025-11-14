<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'date')) $table->date('date')->nullable()->after('id');
            if (!Schema::hasColumn('bookings', 'start_time')) $table->time('start_time')->nullable()->after('date');
            if (!Schema::hasColumn('bookings', 'end_time')) $table->time('end_time')->nullable()->after('start_time');
            if (!Schema::hasColumn('bookings', 'email')) $table->string('email', 150)->nullable()->after('name');
            if (!Schema::hasColumn('bookings', 'phone')) $table->string('phone', 50)->nullable()->after('email');
            if (!Schema::hasColumn('bookings', 'note')) $table->text('note')->nullable()->after('phone');

            $table->string('status', 20)->default('pending')->change(); // pending|confirmed|canceled
            $table->unique(['date','start_time','end_time'], 'booking_unique_slot');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique('booking_unique_slot');
            $table->dropColumn(['date','start_time','end_time','email','phone','note']);
        });
    }
};
