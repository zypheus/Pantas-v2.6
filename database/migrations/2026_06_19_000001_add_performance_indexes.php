<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('book_logs', function (Blueprint $table) {
            $table->index(['student_id', 'status', 'due_date'], 'idx_book_logs_active_loans');
            $table->index(['book_id', 'id'], 'idx_book_logs_book_latest');
            $table->index(['student_id', 'timestamp'], 'idx_book_logs_student_history');
            $table->index(['student_id', 'book_id', 'status', 'returned_date'], 'idx_book_logs_reborrow');
        });

        Schema::table('room_reservations', function (Blueprint $table) {
            $table->index(['student_id', 'date', 'start_time'], 'idx_rr_student_date_time');
            $table->index(['room_id', 'date', 'status'], 'idx_rr_room_date_status');
            $table->index(['student_id', 'status', 'updated_at'], 'idx_rr_student_status_updated');
        });

        Schema::table('books', function (Blueprint $table) {
            $table->index(['archived_at', 'title_statement', 'main_author', 'pub_year'], 'idx_books_catalog_grouping');
            $table->index(['archived_at', 'course'], 'idx_books_course');
            $table->index(['archived_at', 'content_type'], 'idx_books_content_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex('idx_books_catalog_grouping');
            $table->dropIndex('idx_books_course');
            $table->dropIndex('idx_books_content_type');
        });

        Schema::table('room_reservations', function (Blueprint $table) {
            $table->dropIndex('idx_rr_student_date_time');
            $table->dropIndex('idx_rr_room_date_status');
            $table->dropIndex('idx_rr_student_status_updated');
        });

        Schema::table('book_logs', function (Blueprint $table) {
            $table->dropIndex('idx_book_logs_active_loans');
            $table->dropIndex('idx_book_logs_book_latest');
            $table->dropIndex('idx_book_logs_student_history');
            $table->dropIndex('idx_book_logs_reborrow');
        });
    }
};
