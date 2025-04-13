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
        // First, modify the employment_type1 to allow "Not Applicable"
        DB::statement("ALTER TABLE professional_records MODIFY COLUMN employment_type1 ENUM('Private', 'Government', 'NGO/INGO', 'Not Applicable')");

        // Next, modify the employment_type2 to allow "Not Applicable"
        DB::statement("ALTER TABLE professional_records MODIFY COLUMN employment_type2 ENUM('Full-Time', 'Part-Time', 'Traineeship', 'Internship', 'Contract', 'Not Applicable')");

        // Finally, update the monthly_salary enum to match form options case
        DB::statement("ALTER TABLE professional_records MODIFY COLUMN monthly_salary ENUM('No Income', 'Below 10,000', '10,000-20,000', '20,001-40,000', '40,001-60,000', '60,001-80,000', '80,001-100,000', 'Over 100,000')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the changes
        DB::statement("ALTER TABLE professional_records MODIFY COLUMN employment_type1 ENUM('Private', 'Government', 'NGO/INGO')");
        DB::statement("ALTER TABLE professional_records MODIFY COLUMN employment_type2 ENUM('Full-Time', 'Part-Time', 'Traineeship', 'Internship', 'Contract')");
        DB::statement("ALTER TABLE professional_records MODIFY COLUMN monthly_salary ENUM('no income', 'below 10,000', '10,000-20,000', '20,001-40,000', '40,001-60,000', '60,001-80,000', '80,001-100,000', 'over 100,000')");
    }
};
