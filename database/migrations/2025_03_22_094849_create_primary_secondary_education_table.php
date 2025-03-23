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
        Schema::create('primary_secondary_education', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->references('id')
                ->on('users');

            $table->enum('type', ['primary', 'secondary']);
            $table->enum('strand', [
                "STEM",
                "HUMSS",
                "ABM",
                "GAS",
                "Home Economics",
                "ICT",
                "Industrial Arts",
                "Agri-Fishery Arts",
                "Sports Track",
                "Arts and Design Track"
            ]);

            $table->string('school_name');
            $table->string('location');
            $table->year('start');
            $table->year('end');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('primary_secondary_education');
    }
};
