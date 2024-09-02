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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('role', ['Admin', 'Alumni']);

            // Nullable foreign keys
            $table->unsignedBigInteger('personal_bio_id')->nullable();
            $table->unsignedBigInteger('educational_bio_id')->nullable();
            $table->unsignedBigInteger('professional_bio_id')->nullable();

            $table->boolean('is_deleted')->default(0);
            $table->foreignId('department_id')
                ->references('id')
                ->on('departments');

            $table->timestamps();

            // Foreign key constraints
            // $table->foreign('personal_bio_id')->references('id')->on('personal_bio')->onDelete('set null');
            $table->foreign('personal_bio_id')
                ->references('id')
                ->on('personal_bio')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('educational_bio_id')
                ->references('id')
                ->on('educational_bio')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('professional_bio_id')
                ->references('id')
                ->on('professional_bio')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('username')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['personal_bio_id']);
            $table->dropForeign(['educational_bio_id']);
            $table->dropForeign(['professional_bio_id']);
        });

        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
