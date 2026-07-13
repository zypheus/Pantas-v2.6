<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branding_settings', function (Blueprint $table) {
            $table->string('table_header_color', 7)->nullable()->after('button_color');
            $table->string('table_header_text_color', 7)->nullable()->after('table_header_color');
            $table->string('table_border_color', 7)->nullable()->after('table_header_text_color');
            $table->string('table_hover_color', 7)->nullable()->after('table_border_color');
        });
    }

    public function down(): void
    {
        Schema::table('branding_settings', function (Blueprint $table) {
            $table->dropColumn(['table_header_color', 'table_header_text_color', 'table_border_color', 'table_hover_color']);
        });
    }
};
