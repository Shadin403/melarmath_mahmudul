<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Register DhakaArea model for language advanced support
        if (class_exists(\Botble\LanguageAdvanced\Supports\LanguageAdvancedManager::class)) {
            \Botble\LanguageAdvanced\Supports\LanguageAdvancedManager::registerModule(\App\Models\DhakaArea::class, [
                'name',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Unregister DhakaArea model from language advanced support
        if (class_exists(\Botble\LanguageAdvanced\Supports\LanguageAdvancedManager::class)) {
            \Botble\LanguageAdvanced\Supports\LanguageAdvancedManager::removeModule(\App\Models\DhakaArea::class);
        }
    }
};
