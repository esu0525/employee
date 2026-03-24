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
        if (!Schema::hasColumn('employee_requests', 'requirements_file_content')) {
            DB::statement('ALTER TABLE employee_requests ADD COLUMN requirements_file_content LONGBLOB AFTER requirements_file');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_requests', function (Blueprint $table) {
            $table->dropColumn('requirements_file_content');
        });
    }
};
