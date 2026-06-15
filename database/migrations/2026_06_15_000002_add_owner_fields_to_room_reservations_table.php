<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_reservations', function (Blueprint $table) {
            if (! Schema::hasColumn('room_reservations', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('room_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('room_reservations', 'student_id')) {
                $table->foreignId('student_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('students')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('room_reservations', function (Blueprint $table) {
            if (Schema::hasColumn('room_reservations', 'student_id')) {
                $table->dropConstrainedForeignId('student_id');
            }

            if (Schema::hasColumn('room_reservations', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};
