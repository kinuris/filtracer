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
        Schema::create('education_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->enum('school', [
                'Filamer Christian University',
                'University of the Philippines in the Visayas',
                'Central Philippine University',
                'John B. Lacson Foundation Maritime University',
                'University of St. La Salle',
                'West Visayas State University',
                'University of Negros Occidental - Recoletos',
                'University of Iloilo - PHINMA',
                'Iloilo Science and Technology University',
                'Aklan State University',
                'University of San Agustin',
                'Capiz State University',
                'St. Paul University Iloilo',
                'University of Antique',
                'Central Philippine Adventist College',
                'Western Institute of Technology',
                'Guimaras State University',
                'STI West Negros University'
            ]);

            $table->string('school_location');

            $table->enum('degree_type', [
                "Bachelor",
                "Masteral",
                "Doctoral",
            ]);

            $table->foreignId('course_id')
                ->references('id')
                ->on('courses')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreignId('major_id')
                ->nullable()
                ->references('id')
                ->on('majors')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->year('start');
            $table->year('end')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education_records');
    }
};
