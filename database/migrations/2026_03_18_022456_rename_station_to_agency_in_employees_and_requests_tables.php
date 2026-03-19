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
            $table->renameColumn('station', 'agency');
        });

        Schema::table('employee_requests', function (Blueprint $table) {
            $table->renameColumn('station', 'agency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->renameColumn('agency', 'station');
        });

        Schema::table('employee_requests', function (Blueprint $table) {
            $table->renameColumn('agency', 'station');
        });
    }
};
