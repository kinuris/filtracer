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
        Schema::create('skills', function (Blueprint $table) {
            $table->id();

            $table->enum('skill_type', [
                'Soft',
                'Hard'
            ]);

            $table->enum('soft_skill', [
                'Communication Skills',
                'Teamwork and Collaboration',
                'Leadership Skills',
                'Adaptability and Problem-Solving',
                'Time Management',
                'Work Ethic',
                'Interpersonal Skills',
            ])->nullable();

            $table->enum('hard_skill', [
                'Technical Skills',
                'Engineering Skills',
                'Business and Finance Skills',
                'Marketing Skills',
                'Cooking Skills'
            ])->nullable();

            $table->foreignId('user_id')
                ->references('id')
                ->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
