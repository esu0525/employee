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
            $table->string('marital_status', 50)->nullable()->after('sex');
            $table->string('religion', 100)->nullable()->after('marital_status');
            $table->string('blood_type', 10)->nullable()->after('religion');
            $table->string('nationality', 100)->nullable()->after('blood_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['marital_status', 'religion', 'blood_type', 'nationality']);
        });
    }
};
