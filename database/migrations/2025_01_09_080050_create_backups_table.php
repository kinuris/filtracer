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
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->string('backup_id')
                ->unique();

            $table->string('student_id')
                ->unique();

            $table->json('partialPersonalBio');
            $table->json('personalBio');
            $table->json('educationalBios');
            $table->json('professionalBio');
            $table->json('professionalBioFiles');
            $table->json('professionalBioSoftSkills');
            $table->json('professionalBioHardSkills');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};
