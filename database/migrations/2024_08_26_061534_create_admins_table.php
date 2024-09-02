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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('fullname');
            $table->enum('office', [
                'Alumni Office',
                'CAS Faculty Office',
                'CBA Faculty Office',
                'CCS Faculty Office',
                'CCJE Faculty Office',
                'CHTM Faculty Office',
                'CN Faculty Office',
                'COE Faculty Office',
                'CTE Faculty Office',
                'Graduate School Faculty Office'
            ]);
            $table->string('email_address')->unique();
            $table->string('phone_number');
            $table->string('profile_picture')->nullable();
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();
            
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};