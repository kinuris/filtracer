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
        Schema::create('bound_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('admin_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreignId('alumni_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bound_accounts');
    }
};
