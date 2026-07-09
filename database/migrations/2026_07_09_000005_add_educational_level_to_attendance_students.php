<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['attendance_pending_students', 'attendance_students'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (! Schema::hasColumn($tableName, 'educational_level')) {
                    $table->string('educational_level')->nullable()->after('birth_date');
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['attendance_pending_students', 'attendance_students'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (Schema::hasColumn($tableName, 'educational_level')) {
                    $table->dropColumn('educational_level');
                }
            });
        }
    }
};
