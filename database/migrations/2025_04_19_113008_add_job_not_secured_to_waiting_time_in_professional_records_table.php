<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Import DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('professional_records', function (Blueprint $table) {
            // Add 'Job not secured' to waiting_time enum
            DB::statement("ALTER TABLE professional_records MODIFY COLUMN waiting_time ENUM('Below 3 months', '3-5 months', '6 months-1 year', 'Over 1 year', 'Job not secured')");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professional_records', function (Blueprint $table) {
            // Revert waiting_time enum to its previous state
            DB::statement("ALTER TABLE professional_records MODIFY COLUMN waiting_time ENUM('Below 3 months', '3-5 months', '6 months-1 year', 'Over 1 year')");
        });
    }
};
