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
        Schema::table('employee_requests', function (Blueprint $table) {
            $table->integer('num_copies')->default(1)->after('request_type');
            $table->string('school')->nullable()->after('employee_name');
            $table->string('purpose')->nullable()->after('num_copies');
            $table->string('requirements_file')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_requests', function (Blueprint $table) {
            $table->dropColumn(['num_copies', 'school', 'purpose', 'requirements_file']);
        });
    }
};
