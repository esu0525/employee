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
        // For MySQL ENUM columns, we need to re-define the entire list
        DB::statement("ALTER TABLE employees MODIFY COLUMN status ENUM('active', 'inactive', 'resign', 'retired', 'transfer', 'others') DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back if needed (CAUTION: if 'others' data exists it might be truncated)
        DB::statement("ALTER TABLE employees MODIFY COLUMN status ENUM('active', 'inactive', 'resign', 'retired', 'transfer') DEFAULT 'active'");
    }
};
