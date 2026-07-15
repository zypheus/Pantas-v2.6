<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branding_settings', function (Blueprint $table): void {
            $table->string('login_modal_welcome_portal_color', 7)->nullable()->after('login_modal_left_background_color');
            $table->string('login_modal_description_color', 7)->nullable()->after('login_modal_welcome_portal_color');
        });
    }

    public function down(): void
    {
        Schema::table('branding_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'login_modal_welcome_portal_color',
                'login_modal_description_color',
            ]);
        });
    }
};
