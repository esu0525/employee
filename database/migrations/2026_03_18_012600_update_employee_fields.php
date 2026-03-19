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
            // Rename department to station
            if (Schema::hasColumn('employees', 'department')) {
                $table->renameColumn('department', 'station');
            }

            // Drop religion and blood_type
            if (Schema::hasColumn('employees', 'religion')) {
                $table->dropColumn('religion');
            }
            if (Schema::hasColumn('employees', 'blood_type')) {
                $table->dropColumn('blood_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'station')) {
                $table->renameColumn('station', 'department');
            }
            $table->string('religion', 100)->nullable();
            $table->string('blood_type', 10)->nullable();
        });
    }
};
