<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branding_settings', function (Blueprint $table): void {
            $table->string('register_modal_attendance_welcome_portal_color', 7)->nullable()->after('register_modal_attendance_panel_color');
            $table->string('register_modal_attendance_description_color', 7)->nullable()->after('register_modal_attendance_welcome_portal_color');
            $table->string('register_modal_library_welcome_portal_color', 7)->nullable()->after('register_modal_library_panel_color');
            $table->string('register_modal_library_description_color', 7)->nullable()->after('register_modal_library_welcome_portal_color');
        });
    }

    public function down(): void
    {
        Schema::table('branding_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'register_modal_attendance_welcome_portal_color',
                'register_modal_attendance_description_color',
                'register_modal_library_welcome_portal_color',
                'register_modal_library_description_color',
            ]);
        });
    }
};
