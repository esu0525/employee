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
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'profile_picture_content')) {
                $table->dropColumn('profile_picture_content');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'profile_picture_content')) {
                $table->dropColumn('profile_picture_content');
            }
        });

        Schema::table('employee_documents', function (Blueprint $table) {
            if (Schema::hasColumn('employee_documents', 'file_content')) {
                $table->dropColumn('file_content');
            }
        });

        Schema::table('employee_requests', function (Blueprint $table) {
            if (Schema::hasColumn('employee_requests', 'requirements_file_content')) {
                $table->dropColumn('requirements_file_content');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->binary('profile_picture_content')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->binary('profile_picture_content')->nullable();
        });

        Schema::table('employee_documents', function (Blueprint $table) {
            $table->binary('file_content')->nullable();
        });

        Schema::table('employee_requests', function (Blueprint $table) {
            $table->binary('requirements_file_content')->nullable();
        });
    }
};
