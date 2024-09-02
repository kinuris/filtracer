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
        Schema::create('educational_bio', function (Blueprint $table) {
            $table->id();
            $table->enum('school_name', [
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
            ])->nullable();

            $table->string('other_school')->nullable();
            $table->string('school_location');

            $table->enum('degree_type', [
                "Bachelor",
                "Masteral",
                "Doctoral",
            ]);

            $table->string('other_type')->nullable();

            $table->unsignedBigInteger('course_id')->nullable();
            $table->string('other_course')->nullable();

            $table->unsignedBigInteger('major_id')->nullable();
            $table->string('other_major')->nullable();

            $table->year('batch');

            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('major_id')->references('id')->on('majors')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('educational_bio');
    }
};
