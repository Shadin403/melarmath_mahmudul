<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        // Drop the existing tables if they exist before creating new ones
        Schema::dropIfExists('steadfasts_configs');
        Schema::dropIfExists('steadfasts_translations');
        Schema::dropIfExists('steadfasts');

        // Create steadfasts table
        Schema::create('steadfasts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id'); // Foreign key referencing ec_orders
            $table->decimal('steadfast_amount', 15, 2)->nullable(); // Amount field, nullable as per your SQL dump
            $table->string('steadfast_consignment_id', 255)->nullable(); // Consignment ID
            $table->boolean('steadfast_is_sent')->default(false); // Boolean for sent status
            $table->string('stdf_delivery_status', 60)->default('pending'); // Delivery status
            $table->string('tracking_code', 255)->nullable(); // Tracking code
            $table->text('error')->nullable(); // Error field
            $table->timestamps(); // Created_at and updated_at timestamps
            $table->string('status', 255)->nullable(); // Status field

            // Foreign key constraint
            $table->foreign('order_id')->references('id')->on('ec_orders')->onDelete('cascade');
        });

        // Create steadfasts_translations table
        Schema::create('steadfasts_translations', function (Blueprint $table) {
            $table->string('lang_code'); // Language code
            $table->foreignId('steadfasts_id')->constrained('steadfasts')->onDelete('cascade'); // Foreign key referencing steadfasts
            $table->string('name', 255)->nullable(); // Name field
            $table->string('steadfast_consignment_id', 255)->nullable(); // Consignment ID
            $table->string('stdf_delivery_status', 60)->nullable(); // Delivery status
            $table->primary(['lang_code', 'steadfasts_id'], 'steadfasts_translations_primary'); // Composite primary key
        });

        // Create steadfasts_configs table
        Schema::create('steadfasts_configs', function (Blueprint $table) {
            $table->id();
            $table->string('client_name', 255)->nullable(); // client_name
            $table->string('license_code', 255)->nullable(); // license_code
            $table->string('license_file', 255)->nullable(); // license_code
            $table->boolean('enable_disable')->default(false); // Enable/Disable flag
            $table->boolean('notes')->default(false); // Notes checkbox
            $table->string('api_key', 255)->nullable(); // API key
            $table->string('secret_key', 255)->nullable(); // Secret key
            $table->string('business_name', 255)->nullable(); // Business name
            $table->string('business_address', 255)->nullable(); // Business address
            $table->string('business_email', 255)->nullable(); // Business email
            $table->string('business_number', 255)->nullable(); // Business number
            $table->text('terms_conditions')->nullable(); // Terms and conditions
            $table->string('business_logo')->nullable(); // Business logo file path
            $table->string('callback_url', 255)->nullable(); // Callback URL
            $table->string('auth_token', 255)->nullable(); // Auth token
            $table->boolean('use_as_invoice_info')->default(false); // Use as invoice info
            $table->boolean('bdcourier_API')->default(false); // bdcourier API enabled
            $table->string('bdcourier_auth', 255)->nullable(); // bdcourier Auth key
            $table->timestamps(); // Timestamps
        });
    }

    public function down(): void
    {
        // Drop tables if they exist
        Schema::dropIfExists('steadfasts_configs');
        Schema::dropIfExists('steadfasts_translations');
        Schema::dropIfExists('steadfasts');
    }
};
