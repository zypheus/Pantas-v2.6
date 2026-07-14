<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branding_settings', function (Blueprint $table): void {
            $table->string('register_modal_attendance_logo_path')->nullable()->after('login_modal_logo_path');
            $table->string('register_modal_library_logo_path')->nullable()->after('register_modal_attendance_logo_path');
            $table->string('register_modal_heading', 80)->nullable()->after('login_modal_password_placeholder');
            $table->string('register_modal_login_label', 80)->nullable()->after('register_modal_heading');
            $table->string('register_modal_attendance_tab', 80)->nullable()->after('register_modal_login_label');
            $table->string('register_modal_library_tab', 80)->nullable()->after('register_modal_attendance_tab');
            $table->string('register_modal_attendance_welcome_label', 80)->nullable()->after('register_modal_library_tab');
            $table->string('register_modal_attendance_portal_name', 100)->nullable()->after('register_modal_attendance_welcome_label');
            $table->string('register_modal_attendance_description', 255)->nullable()->after('register_modal_attendance_portal_name');
            $table->string('register_modal_attendance_heading', 120)->nullable()->after('register_modal_attendance_description');
            $table->string('register_modal_attendance_student_label', 80)->nullable()->after('register_modal_attendance_heading');
            $table->string('register_modal_attendance_employee_label', 80)->nullable()->after('register_modal_attendance_student_label');
            $table->string('register_modal_attendance_student_submit', 120)->nullable()->after('register_modal_attendance_employee_label');
            $table->string('register_modal_attendance_employee_submit', 120)->nullable()->after('register_modal_attendance_student_submit');
            $table->string('register_modal_library_welcome_label', 80)->nullable()->after('register_modal_attendance_employee_submit');
            $table->string('register_modal_library_portal_name', 100)->nullable()->after('register_modal_library_welcome_label');
            $table->string('register_modal_library_description', 255)->nullable()->after('register_modal_library_portal_name');
            $table->string('register_modal_library_heading', 120)->nullable()->after('register_modal_library_description');
            $table->string('register_modal_library_student_label', 80)->nullable()->after('register_modal_library_heading');
            $table->string('register_modal_library_employee_label', 80)->nullable()->after('register_modal_library_student_label');
            $table->string('register_modal_library_student_submit', 120)->nullable()->after('register_modal_library_employee_label');
            $table->string('register_modal_library_employee_submit', 120)->nullable()->after('register_modal_library_student_submit');
            $table->string('register_modal_attendance_panel_color', 7)->nullable()->after('login_modal_button_color');
            $table->string('register_modal_attendance_text_color', 7)->nullable()->after('register_modal_attendance_panel_color');
            $table->string('register_modal_attendance_accent_color', 7)->nullable()->after('register_modal_attendance_text_color');
            $table->string('register_modal_attendance_active_role_color', 7)->nullable()->after('register_modal_attendance_accent_color');
            $table->string('register_modal_attendance_submit_color', 7)->nullable()->after('register_modal_attendance_active_role_color');
            $table->string('register_modal_library_panel_color', 7)->nullable()->after('register_modal_attendance_submit_color');
            $table->string('register_modal_library_text_color', 7)->nullable()->after('register_modal_library_panel_color');
            $table->string('register_modal_library_accent_color', 7)->nullable()->after('register_modal_library_text_color');
            $table->string('register_modal_library_active_role_color', 7)->nullable()->after('register_modal_library_accent_color');
            $table->string('register_modal_library_submit_color', 7)->nullable()->after('register_modal_library_active_role_color');
        });
    }

    public function down(): void
    {
        Schema::table('branding_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'register_modal_attendance_logo_path',
                'register_modal_library_logo_path',
                'register_modal_heading',
                'register_modal_login_label',
                'register_modal_attendance_tab',
                'register_modal_library_tab',
                'register_modal_attendance_welcome_label',
                'register_modal_attendance_portal_name',
                'register_modal_attendance_description',
                'register_modal_attendance_heading',
                'register_modal_attendance_student_label',
                'register_modal_attendance_employee_label',
                'register_modal_attendance_student_submit',
                'register_modal_attendance_employee_submit',
                'register_modal_library_welcome_label',
                'register_modal_library_portal_name',
                'register_modal_library_description',
                'register_modal_library_heading',
                'register_modal_library_student_label',
                'register_modal_library_employee_label',
                'register_modal_library_student_submit',
                'register_modal_library_employee_submit',
                'register_modal_attendance_panel_color',
                'register_modal_attendance_text_color',
                'register_modal_attendance_accent_color',
                'register_modal_attendance_active_role_color',
                'register_modal_attendance_submit_color',
                'register_modal_library_panel_color',
                'register_modal_library_text_color',
                'register_modal_library_accent_color',
                'register_modal_library_active_role_color',
                'register_modal_library_submit_color',
            ]);
        });
    }
};