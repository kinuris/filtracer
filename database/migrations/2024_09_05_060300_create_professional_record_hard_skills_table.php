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
        Schema::create('professional_record_hard_skills', function (Blueprint $table) {
            $table->id();

            $table->foreignId('professional_record_id')
                ->references('id')
                ->on('professional_records')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->enum('skill', [
                'Technical Skills',
                'Engineering Skills',
                'Business and Finance Skills',
                'Marketing Skills',
                'Cooking Skills'
            ]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_record_hard_skills');
    }
};
