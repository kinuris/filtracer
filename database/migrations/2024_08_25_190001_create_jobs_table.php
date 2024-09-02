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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('job_title');
            $table->enum('employment_type', [
                'Full-Time Employment', 
                'Part-Time Employment', 
                'Internship', 
                'Casual Employment', 
                'Contract Employment',
                'Apprenticeship',
                'Traineeship',
                'Employment on Commission',
                'Probation'
            ]);
            $table->string('company');
            $table->string('work_location');
            $table->text('description')->nullable();
            $table->string('source_link')->nullable();
            $table->enum('status', ['Open', 'Close']);

            $table->unsignedBigInteger('user_id');
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};