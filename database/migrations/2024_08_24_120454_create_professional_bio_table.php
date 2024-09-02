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
        Schema::create('professional_bio', function (Blueprint $table) {
            $table->id();

            //employment
            $table->enum('employment_status', [
                'Employed',
                'Unemployed',
                'Self-employed',
                'Student',
                'Working Student',
                'Retired'
            ]);
            $table->string('business_name')->nullable();
            $table->string('business_type')->nullable();
            $table->string('business_role')->nullable();
            $table->string('contact_number')->nullable();

            $table->enum('employment_type1', [
                'Private',
                'Government',
                'NGO/INGO'
            ])->nullable();

            $table->enum('employment_type2', [
                'Full-Time',
                'Part-Time',
                'Traineeship',
                'Internship',
                'Contract'
            ])->nullable();

            $table->enum('monthly_salary', [
                'No Income',
                'Below 10,000',
                '10,000-20,000',
                '20,001-40,000',
                '40,001-60,000',
                '60,001-80,000',
                '80,001-100,000',
                'Over 100,000'
            ]);

            $table->string('job_title')->nullable();
            $table->string('company_name')->nullable();
            $table->string('industry')->nullable();
            $table->string('work_location')->nullable();

            //employability
            $table->enum('job_search_methods', [
                'Career Center',
                'Experimental Learning',
                'Networking',
                'Online Resources',
                'Campus Resources'
            ])->nullable();

            $table->enum('waiting_time', [
                'Below 3 months',
                '3-5 months',
                '6 months-1 year',
                'Over 1 year'
            ])->nullable();

            $table->enum('hard_skills', [
                'Technical Skills',
                'Engineering Skills',
                'Business and Finance Skills',
                'Marketing Skills',
                'Cooking Skills'
            ])->nullable();

            $table->enum('soft_skills', [
                'Communication Skills',
                'Teamwork and Collaboration',
                'Leadership Skills',
                'Adaptability and Problem-Solving',
                'Time Management',
                'Work Ethic',
                'Interpersonal Skills'
            ])->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_bio');
    }
};
