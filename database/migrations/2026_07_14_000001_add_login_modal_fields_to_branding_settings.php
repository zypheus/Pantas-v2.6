<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branding_settings', function (Blueprint $table): void {
            $table->string('login_modal_logo_path')->nullable()->after('sidebar_logo_path');
            $table->string('login_modal_welcome_label', 80)->nullable()->after('sidebar_brand_text_color');
            $table->string('login_modal_portal_name', 100)->nullable()->after('login_modal_welcome_label');
            $table->string('login_modal_description', 255)->nullable()->after('login_modal_portal_name');
            $table->string('login_modal_sign_in_heading', 120)->nullable()->after('login_modal_description');
            $table->string('login_modal_email_placeholder', 120)->nullable()->after('login_modal_sign_in_heading');
            $table->string('login_modal_password_placeholder', 120)->nullable()->after('login_modal_email_placeholder');
            $table->string('login_modal_left_background_color', 7)->nullable()->after('table_hover_color');
            $table->string('login_modal_background_color', 7)->nullable()->after('login_modal_left_background_color');
            $table->string('login_modal_text_color', 7)->nullable()->after('login_modal_background_color');
            $table->string('login_modal_button_color', 7)->nullable()->after('login_modal_text_color');
        });
    }

    public function down(): void
    {
        Schema::table('branding_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'login_modal_logo_path',
                'login_modal_welcome_label',
                'login_modal_portal_name',
                'login_modal_description',
                'login_modal_sign_in_heading',
                'login_modal_email_placeholder',
                'login_modal_password_placeholder',
                'login_modal_left_background_color',
                'login_modal_background_color',
                'login_modal_text_color',
                'login_modal_button_color',
            ]);
        });
    }
};
