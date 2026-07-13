<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branding_settings', function (Blueprint $table) {
            $table->string('opac_logo_path')->nullable()->after('opac_banner_path');
            $table->string('opac_default_book_cover_path')->nullable()->after('opac_logo_path');
        });
    }

    public function down(): void
    {
        Schema::table('branding_settings', function (Blueprint $table) {
            $table->dropColumn(['opac_logo_path', 'opac_default_book_cover_path']);
        });
    }
};