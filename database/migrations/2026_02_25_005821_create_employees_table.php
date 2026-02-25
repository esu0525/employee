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
        Schema::create('employees', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('position');
            $table->string('department');
            $table->string('email');
            $table->string('phone');
            $table->date('date_joined');
            $table->enum('status', ['active', 'inactive', 'resign', 'retired', 'transfer'])->default('active');
            $table->date('status_date')->nullable();
            $table->string('transfer_location')->nullable();
            $table->string('address')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
