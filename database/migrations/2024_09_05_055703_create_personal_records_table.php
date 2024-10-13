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
        Schema::create('personal_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->string('student_id');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->date('birthdate');
            $table->enum('civil_status', [
                'Married',
                'Single',
                'Divorced',
                'Widowed',
                'Separated'
            ]);

            $table->string('permanent_address');
            $table->string('current_address');
            $table->string('email_address')->unique();
            $table->string('phone_number');
            $table->string('social_link')->nullable();
            $table->string('profile_picture')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0 = Unverified, 1 = Verified');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_records');
    }
};
