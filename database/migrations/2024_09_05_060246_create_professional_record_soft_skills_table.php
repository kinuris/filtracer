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
        Schema::create('professional_record_soft_skills', function (Blueprint $table) {
            $table->id();

            $table->foreignId('professional_record_id')
                ->references('id')
                ->on('professional_records');

            $table->enum('skill', [
                'Communication Skills',
                'Teamwork and Collaboration',
                'Leadership Skills',
                'Adaptability and Problem-Solving',
                'Time Management',
                'Work Ethic',
                'Interpersonal Skills'
            ]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_record_soft_skills');
    }
};
