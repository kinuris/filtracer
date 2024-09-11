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
        Schema::create('professional_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->references('id')
                ->on('users');

            $table->enum('employment_status', [
                'Employed',
                'Unemployed',
                'Self-employed',
                'Student',
                'Working Student',
                'Retired'
            ]);

            $table->enum('employment_type1', [
                'Private',
                'Government',
                'NGO/INGO'
            ]);

            $table->enum('employment_type2', [
                'Full-Time',
                'Part-Time',
                'Traineeship',
                'Internship',
                'Contract'
            ]);

            $table->enum('monthly_salary', [
                'no income',
                'below 10,000',
                '10,000-20,000',
                '20,001-40,000',
                '40,001-60,000',
                '60,001-80,000',
                '80,001-100,000',
                'over 100,000'
            ]);

            $table->string('job_title');
            $table->string('company_name');
            $table->string('industry');
            $table->string('work_location');

            $table->enum('waiting_time', [
                'Below 3 months',
                '3-5 months',
                '6 months-1 year',
                'Over 1 year'
            ]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_records');
    }
};
