<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['attendance_pending_employees', 'attendance_employees'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (! Schema::hasColumn($tableName, 'mobile_number')) {
                    $table->string('mobile_number', 20)->nullable()->after('birth_date');
                }

                if (! Schema::hasColumn($tableName, 'emergency_address')) {
                    $table->text('emergency_address')->nullable()->after('address');
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['attendance_pending_employees', 'attendance_employees'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (Schema::hasColumn($tableName, 'emergency_address')) {
                    $table->dropColumn('emergency_address');
                }

                if (Schema::hasColumn($tableName, 'mobile_number')) {
                    $table->dropColumn('mobile_number');
                }
            });
        }
    }
};
