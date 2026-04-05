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
        Schema::create('archive_reports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('period_coverage')->nullable();
            $table->string('regional_office')->nullable();
            $table->string('file_name');
            $table->string('format'); // 'pdf' or 'excel'
            $table->json('employee_ids'); // JSON array of employee IDs included
            $table->integer('employee_count')->default(0);
            $table->string('generated_by')->nullable(); // user who generated
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archive_reports');
    }
};
