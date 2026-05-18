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
        Schema::table('users', function (Blueprint $table) {
            $table->index(
                ['organisation_id', 'division_id', 'role'],
                'users_org_div_role_idx'
            );
        });

        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->index(
                ['organisation_id', 'division_id', 'session_date'],
                'att_sess_org_div_date_idx'
            );

            $table->index('is_active', 'att_sess_active_idx');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->index(
                ['session_id', 'user_id', 'status'],
                'att_session_user_status_idx'
            );
        });

        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->index(
                ['user_id', 'qr_token'],
                'att_logs_user_qr_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_org_div_role_idx');
        });

        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->dropIndex('att_sess_org_div_date_idx');
            $table->dropIndex('att_sess_active_idx');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex('att_session_user_status_idx');
        });

        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropIndex('att_logs_user_qr_idx');
        });
    }
};
