<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained('attendance_students')->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('attendance_employees')->cascadeOnDelete();
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
        Schema::dropIfExists('attendance_logs');
    }
};
