<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change request_type from enum to string to support the new document types
        Schema::table('employee_requests', function (Blueprint $table) {
            $table->string('request_type', 100)->change();
        });
    }

    public function down(): void
    {
        Schema::table('employee_requests', function (Blueprint $table) {
            $table->string('request_type', 100)->change();
        });
    }
};
