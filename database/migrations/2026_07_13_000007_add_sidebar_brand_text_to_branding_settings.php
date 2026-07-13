<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branding_settings', function (Blueprint $table) {
            $table->string('sidebar_brand_name', 60)->nullable()->after('sidebar_logo_path');
            $table->string('sidebar_brand_subtitle', 100)->nullable()->after('sidebar_brand_name');
            $table->string('sidebar_brand_text_color', 7)->nullable()->after('sidebar_brand_subtitle');
        });
    }

    public function down(): void
    {
        Schema::table('branding_settings', function (Blueprint $table) {
            $table->dropColumn(['sidebar_brand_name', 'sidebar_brand_subtitle', 'sidebar_brand_text_color']);
        });
    }
};
