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
        Schema::create('chatroom', function (Blueprint $table) {
            $table->id();
            $table->string('chat_name');
            // NOTE: Why not use created_at generated by timestamps()? $table->dateTime('date_created')->nullable();
            $table->unsignedBigInteger('user_id'); // NOTE: Not entirely sure why this is needed at all
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatroom');
    }
};