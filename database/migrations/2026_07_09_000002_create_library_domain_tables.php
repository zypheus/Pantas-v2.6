<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_roles', function (Blueprint $table) {
            $table->id();
            $table->string('description')->unique();
            $table->timestamps();
        });

        Schema::create('library_programs', function (Blueprint $table) {
            $table->id();
            $table->string('program_code')->unique();
            $table->string('program_name');
            $table->unsignedTinyInteger('total_years')->default(4);
            $table->timestamps();
        });

        Schema::create('library_program_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('library_programs')->cascadeOnDelete();
            $table->unsignedTinyInteger('year_level');
            $table->timestamps();

            $table->unique(['program_id', 'year_level']);
        });

        Schema::create('library_program_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->nullable()->constrained('library_programs')->cascadeOnDelete();
            $table->foreignId('program_year_id')->nullable()->constrained('library_program_years')->nullOnDelete();
            $table->unsignedTinyInteger('year_number')->nullable();
            $table->string('course_code')->nullable();
            $table->string('course_name');
            $table->timestamps();
        });

        Schema::create('library_catalog_frameworks', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('library_marc_fields', function (Blueprint $table) {
            $table->id();
            $table->string('tag', 10);
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('repeatable')->default(false);
            $table->boolean('required')->default(false);
            $table->json('select_options')->nullable();
            $table->timestamps();
        });

        Schema::create('library_catalog_framework_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_framework_id')->constrained('library_catalog_frameworks')->cascadeOnDelete();
            $table->foreignId('marc_field_id')->constrained('library_marc_fields')->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['catalog_framework_id', 'marc_field_id']);
        });

        Schema::create('library_pending_students', function (Blueprint $table) {
            $table->id();
            $this->studentColumns($table, pending: true);
            $table->timestamps();
        });

        Schema::create('library_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $this->studentColumns($table, pending: false);
            $table->timestamps();
        });

        Schema::create('library_pending_employees', function (Blueprint $table) {
            $table->id();
            $this->employeeColumns($table, pending: true);
            $table->timestamps();
        });

        Schema::create('library_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $this->employeeColumns($table, pending: false);
            $table->timestamps();
        });

        Schema::create('library_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('library_holidays', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable()->unique();
            $table->date('holiday_date')->nullable()->unique();
            $table->string('name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('library_fine_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('loan_duration_days')->default(7);
            $table->decimal('daily_fine', 8, 2)->default(0);
            $table->decimal('fine_per_day', 8, 2)->default(0);
            $table->decimal('max_fine', 8, 2)->nullable();
            $table->integer('grace_period_days')->default(0);
            $table->date('effective_from')->nullable();
            $table->unsignedInteger('max_renewals')->default(0);
            $table->unsignedInteger('renewal_duration_days')->default(7);
            $table->timestamps();
        });

        Schema::create('library_books', function (Blueprint $table) {
            $table->id();
            $this->bookColumns($table);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('library_book_marc_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('library_books')->cascadeOnDelete();
            $table->string('tag', 10);
            $table->string('subfield', 10)->nullable();
            $table->string('indicator1', 10)->nullable();
            $table->string('indicator2', 10)->nullable();
            $table->unsignedInteger('occurrence')->default(0);
            $table->text('value')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('library_book_program', function (Blueprint $table) {
            $table->foreignId('book_id')->constrained('library_books')->cascadeOnDelete();
            $table->foreignId('program_id')->constrained('library_programs')->cascadeOnDelete();

            $table->primary(['book_id', 'program_id']);
        });

        Schema::create('library_book_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('library_books')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('library_students')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('library_employees')->nullOnDelete();
            $table->string('patron_name')->nullable();
            $table->string('status');
            $table->string('circulation_type', 20);
            $table->unsignedTinyInteger('renew_count')->default(0);
            $table->dateTime('last_renewed_at')->nullable();
            $table->dateTime('timestamp')->nullable();
            $table->date('due_date')->nullable();
            $table->dateTime('returned_date')->nullable();
            $table->decimal('fine_incurred', 8, 2)->nullable();
            $table->decimal('fine_original', 8, 2)->nullable();
            $table->decimal('fine_balance', 8, 2)->nullable();
            $table->decimal('fine_paid_total', 8, 2)->nullable();
            $table->decimal('fine_waived_total', 8, 2)->nullable();
            $table->timestamp('fine_cleared_at')->nullable();
            $table->string('fine_clearance_type', 32)->nullable();
            $table->text('fine_clearance_note')->nullable();
            $table->foreignId('fine_cleared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['student_id', 'status']);
            $table->index(['employee_id', 'status']);
        });

        Schema::create('library_book_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('library_books')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('library_students')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('library_employees')->nullOnDelete();
            $table->string('patron_name')->nullable();
            $table->string('patron_email')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('library_ebooks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('publisher')->nullable();
            $table->string('publication_year')->nullable();
            $table->string('source')->nullable();
            $table->string('link')->nullable();
            $table->foreignId('program_id')->nullable()->constrained('library_programs')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('library_program_courses')->nullOnDelete();
            $table->string('pub_year')->nullable();
            $table->string('course')->nullable();
            $table->string('program')->nullable();
            $table->string('file_path')->nullable();
            $table->string('cover_image')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('library_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('capacity')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('library_room_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('library_rooms')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('library_students')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('library_employees')->nullOnDelete();
            $table->string('status');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('patron_email')->nullable();
            $table->unsignedTinyInteger('number_of_students')->default(1);
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('library_prospectuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->nullable()->constrained('library_programs')->nullOnDelete();
            $table->string('course_code')->nullable();
            $table->string('course_name');
            $table->unsignedTinyInteger('year_number')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('library_student_edit_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('library_students')->cascadeOnDelete();
            $this->profileRequestColumns($table);
            $table->timestamps();
        });

        Schema::create('library_employee_edit_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('library_employees')->cascadeOnDelete();
            $this->profileRequestColumns($table);
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->timestamps();
        });

        Schema::create('library_reservation_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_reservation_id')->nullable()->constrained('library_room_reservations')->cascadeOnDelete();
            $table->foreignId('reservation_id')->nullable()->constrained('library_room_reservations')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('library_reservation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_reservation_id')->nullable()->constrained('library_room_reservations')->nullOnDelete();
            $table->foreignId('reservation_id')->nullable()->constrained('library_room_reservations')->nullOnDelete();
            $table->string('action');
            $table->text('meta')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('library_files', function (Blueprint $table) {
            $table->id();
            $table->string('folder')->nullable();
            $table->string('filename')->nullable();
            $table->string('filepath')->nullable();
            $table->string('title')->nullable();
            $table->string('original_name')->nullable();
            $table->string('path')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('library_feedback', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->text('comments');
            $table->timestamps();
        });

        Schema::create('library_attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('library_attendance_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained('library_students')->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('library_employees')->cascadeOnDelete();
            $table->string('rating', 32)->nullable();
            $table->boolean('declined')->default(false);
            $table->timestamps();
        });

        Schema::create('library_attendance_videos', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('library_attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained('library_students')->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('library_employees')->cascadeOnDelete();
            $table->enum('status', ['IN', 'OUT']);
            $table->string('section')->nullable();
            $table->timestamp('scanned_at');
            $table->timestamps();

            $table->index(['student_id', 'scanned_at']);
            $table->index(['employee_id', 'scanned_at']);
        });
    }

    public function down(): void
    {
        foreach ([
            'library_attendance_logs',
            'library_attendance_videos',
            'library_attendance_feedbacks',
            'library_attendance_settings',
            'library_files',
            'library_feedback',
            'library_reservation_logs',
            'library_reservation_students',
            'library_employee_edit_requests',
            'library_student_edit_requests',
            'library_prospectuses',
            'library_room_reservations',
            'library_rooms',
            'library_ebooks',
            'library_book_reservations',
            'library_book_logs',
            'library_book_program',
            'library_book_marc_fields',
            'library_books',
            'library_fine_settings',
            'library_holidays',
            'library_settings',
            'library_employees',
            'library_pending_employees',
            'library_students',
            'library_pending_students',
            'library_catalog_framework_fields',
            'library_marc_fields',
            'library_catalog_frameworks',
            'library_program_courses',
            'library_program_years',
            'library_programs',
            'library_roles',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }

    private function studentColumns(Blueprint $table, bool $pending): void
    {
        $table->string('id_number')->nullable()->unique();
        $table->string('lastname');
        $table->string('firstname');
        $table->string('middle_initial')->nullable();
        $table->date('birthday')->nullable();
        $table->string('qrcode')->nullable()->unique();
        $table->string('course')->nullable();
        $table->string('year')->nullable();
        $table->string('email')->nullable();
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
        $table->foreignId('role_id')->nullable()->constrained('library_roles')->nullOnDelete();
        $table->string('firstname');
        $table->string('lastname');
        $table->string('middle_initial')->nullable();
        $table->string('department')->nullable();
        $table->string('position')->nullable();
        $table->string('employee_id')->nullable()->unique();
        $table->date('birth_date')->nullable();
        $table->string('sex', 20)->nullable();
        $table->string('civil_status', 50)->nullable();
        $table->string('blood_type', 10)->nullable();
        $table->string('designation')->nullable();
        $table->string('program')->nullable();
        $table->string('year_start_work')->nullable();
        $table->string('employee_number')->nullable()->unique();
        $table->string('mobile_number', 20)->nullable();
        $table->string('tin_id_number')->nullable();
        $table->string('philhealth_number')->nullable();
        $table->string('sss_number')->nullable();
        $table->string('hdmf_number')->nullable();
        $table->string('qrcode')->nullable()->unique();
        $table->string('formal_picture')->nullable();
        $table->string('email')->nullable();
        $table->string('emergency_contact_name')->nullable();
        $table->string('emergency_contact_relationship')->nullable();
        $table->string('emergency_contact_number', 20)->nullable();
        $table->text('address')->nullable();
        $table->text('emergency_address')->nullable();
        $table->string('employee_signature')->nullable();

        if (! $pending) {
            $table->string('normalized_name')->nullable()->index();
        }
    }

    private function bookColumns(Blueprint $table): void
    {
        $table->string('control_no')->nullable();
        $table->string('date_time_stamp')->nullable();
        $table->string('fixed_length_data')->nullable();
        $table->string('isbn')->nullable();
        $table->string('price')->nullable();
        $table->string('cataloging_source_a')->nullable();
        $table->string('cataloging_source_b')->nullable();
        $table->string('cataloging_source_e')->nullable();
        $table->string('main_author')->nullable();
        $table->string('title_statement')->nullable();
        $table->string('title_author')->nullable();
        $table->string('edition')->nullable();
        $table->string('pub_place')->nullable();
        $table->string('publisher')->nullable();
        $table->string('pub_year')->nullable();
        $table->string('pages')->nullable();
        $table->string('illustrations')->nullable();
        $table->string('size')->nullable();
        $table->string('volume')->nullable();
        $table->string('content_type')->nullable();
        $table->string('content_code')->nullable();
        $table->string('media_type')->nullable();
        $table->string('media_code')->nullable();
        $table->string('carrier_type')->nullable();
        $table->string('carrier_code')->nullable();
        $table->string('series_title')->nullable();
        $table->text('general_note')->nullable();
        $table->text('bibliography_note')->nullable();
        $table->string('source_vendor')->nullable();
        $table->date('source_date')->nullable();
        $table->string('subject_topic')->nullable();
        $table->string('subject_form')->nullable();
        $table->string('genre')->nullable();
        $table->string('library_name')->nullable();
        $table->string('section')->nullable();
        $table->string('call_number')->nullable();
        $table->string('accession_no')->nullable()->unique();
        $table->string('barcode')->nullable()->unique();
        $table->string('rfid')->nullable()->unique();
        $table->string('availability')->default('Available');
        $table->string('year')->nullable();
        $table->string('course')->nullable();
        $table->string('cover_image')->nullable();
        $table->string('curriculum')->nullable();
        $table->timestamp('archived_at')->nullable();
    }

    private function profileRequestColumns(Blueprint $table): void
    {
        $table->string('lastname')->nullable();
        $table->string('firstname')->nullable();
        $table->string('middle_initial')->nullable();
        $table->date('birthday')->nullable();
        $table->string('course')->nullable();
        $table->string('year')->nullable();
        $table->string('email')->nullable();
        $table->string('mobile_number')->nullable();
        $table->text('address')->nullable();
        $table->string('emergency_person')->nullable();
        $table->string('emergency_relationship')->nullable();
        $table->string('emergency_number')->nullable();
        $table->text('emergency_address')->nullable();
        $table->string('profile_picture')->nullable();
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        $table->text('admin_note')->nullable();
        $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamp('reviewed_at')->nullable();
    }
};
