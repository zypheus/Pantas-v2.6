<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_programs', function (Blueprint $table) {
            $table->id();
            $table->string('program_code')->unique();
            $table->string('program_name');
            $table->unsignedTinyInteger('total_years')->default(4);
            $table->timestamps();
        });

        Schema::create('attendance_program_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('attendance_programs')->cascadeOnDelete();
            $table->unsignedTinyInteger('year_number');
            $table->string('label');
            $table->timestamps();

            $table->unique(['program_id', 'year_number']);
        });

        Schema::create('attendance_program_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('attendance_programs')->cascadeOnDelete();
            $table->foreignId('program_year_id')->nullable()->constrained('attendance_program_years')->nullOnDelete();
            $table->string('course_code')->nullable();
            $table->string('course_name');
            $table->timestamps();
        });

        Schema::create('attendance_pending_students', function (Blueprint $table) {
            $table->id();
            $this->studentColumns($table, pending: true);
            $table->timestamps();
        });

        Schema::create('attendance_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $this->studentColumns($table, pending: false);
            $table->timestamps();
        });

        Schema::create('attendance_pending_employees', function (Blueprint $table) {
            $table->id();
            $this->employeeColumns($table, pending: true);
            $table->timestamps();
        });

        Schema::create('attendance_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $this->employeeColumns($table, pending: false);
            $table->timestamps();
        });

        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_settings');
        Schema::dropIfExists('attendance_employees');
        Schema::dropIfExists('attendance_pending_employees');
        Schema::dropIfExists('attendance_students');
        Schema::dropIfExists('attendance_pending_students');
        Schema::dropIfExists('attendance_program_courses');
        Schema::dropIfExists('attendance_program_years');
        Schema::dropIfExists('attendance_programs');
    }

    private function studentColumns(Blueprint $table, bool $pending): void
    {
        $table->string('student_id')->nullable()->unique();
        $table->string('lastname');
        $table->string('firstname');
        $table->string('middle_initial')->nullable();
        $table->date('birth_date')->nullable();
        $table->string('blood_type', 10)->nullable();
        $table->string('qrcode')->nullable()->unique();
        $table->string('course')->nullable();
        $table->string('year')->nullable();
        $table->string('mobile_number', 20)->nullable();
        $table->text('address')->nullable();
        $table->string('emergency_person')->nullable();
        $table->string('emergency_relationship')->nullable();
        $table->string('emergency_number', 20)->nullable();
        $table->text('emergency_address')->nullable();
        $table->string('profile_picture')->nullable();
        $table->string('student_signature')->nullable();

        if (! $pending) {
            $table->string('normalized_name')->nullable()->index();
        }
    }

    private function employeeColumns(Blueprint $table, bool $pending): void
    {
        $table->string('employee_id')->nullable()->unique();
        $table->string('employee_number')->nullable()->unique();
        $table->string('firstname');
        $table->string('lastname');
        $table->string('middle_initial')->nullable();
        $table->string('department')->nullable();
        $table->string('position')->nullable();
        $table->date('birth_date')->nullable();
        $table->string('sex', 20)->nullable();
        $table->string('civil_status', 50)->nullable();
        $table->string('blood_type', 10)->nullable();
        $table->string('tin_id_number')->nullable();
        $table->string('philhealth_number')->nullable();
        $table->string('sss_number')->nullable();
        $table->string('hdmf_number')->nullable();
        $table->string('qrcode')->nullable()->unique();
        $table->string('formal_picture')->nullable();
        $table->string('emergency_contact_name')->nullable();
        $table->string('emergency_contact_relationship')->nullable();
        $table->string('emergency_contact_number', 20)->nullable();
        $table->text('address')->nullable();
        $table->string('employee_signature')->nullable();

        if (! $pending) {
            $table->string('normalized_name')->nullable()->index();
        }
    }
};
