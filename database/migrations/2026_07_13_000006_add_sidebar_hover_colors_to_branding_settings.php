<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branding_settings', function (Blueprint $table) {
            $table->string('sidebar_hover_background_color', 7)->nullable()->after('sidebar_active_color');
            $table->string('sidebar_hover_text_color', 7)->nullable()->after('sidebar_hover_background_color');
        });
    }

    public function down(): void
    {
        Schema::table('branding_settings', function (Blueprint $table) {
            $table->dropColumn(['sidebar_hover_background_color', 'sidebar_hover_text_color']);
        });
    }
};
