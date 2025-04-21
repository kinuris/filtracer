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
        Schema::table('partial_personal_records', function (Blueprint $table) {
            // Drop the unique constraint first
            $table->dropUnique(['student_id']);
            // Make the column nullable
            $table->string('student_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partial_personal_records', function (Blueprint $table) {
            // Make the column non-nullable again
            // Note: Ensure no null values exist before running this in production if needed
            $table->string('student_id')->nullable(false)->change();
            // Re-add the unique constraint
            $table->unique('student_id');
        });
    }
};
