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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('content');
            $table->string('source')->nullable();

            $table->enum('post_category', ['Event', 'Job Opening', 'Announcement']);
            $table->enum('post_status', ['Incoming', 'Ended', 'Open', 'Closed'])->nullable();

            $table->string('attached_image')->nullable();

            $table->foreignId('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->enum('status', ['Pending', 'Denied', 'Approved'])
                ->default('Pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
