<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Implementing International Performance Standards (Indexing for Scalability).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index(['organisation_id', 'division_id', 'role']);
        });

        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->index(['organisation_id', 'division_id', 'session_date']);
            $table->index('is_active');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->index(['session_id', 'user_id', 'status']);
        });

        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->index(['user_id', 'qr_token']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['organisation_id', 'division_id', 'role']);
        });

        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->dropIndex(['organisation_id', 'division_id', 'session_date']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['session_id', 'user_id', 'status']);
        });

        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'qr_token']);
        });
    }
};
