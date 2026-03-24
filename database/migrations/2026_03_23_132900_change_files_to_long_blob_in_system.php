<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // employees table
        if (!Schema::hasColumn('employees', 'profile_picture_content')) {
            DB::statement('ALTER TABLE employees ADD COLUMN profile_picture_content LONGBLOB AFTER profile_picture');
        }

        // users table
        if (!Schema::hasColumn('users', 'profile_picture_content')) {
            DB::statement('ALTER TABLE users ADD COLUMN profile_picture_content LONGBLOB AFTER profile_picture');
        }

        // employee_documents table
        if (!Schema::hasColumn('employee_documents', 'file_content')) {
            DB::statement('ALTER TABLE employee_documents ADD COLUMN file_content LONGBLOB AFTER file_path');
        }
        
        // request_attachments - just in case there is any file in the request itself
        // But mainly it is documents.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) { $table->dropColumn('profile_picture_content'); });
        Schema::table('users', function (Blueprint $table) { $table->dropColumn('profile_picture_content'); });
        Schema::table('employee_documents', function (Blueprint $table) { $table->dropColumn('file_content'); });
    }
};
