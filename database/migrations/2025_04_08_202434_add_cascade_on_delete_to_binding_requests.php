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
        Schema::table('binding_requests', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropForeign(['alumni_id']);

            $table->foreign('admin_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('alumni_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('binding_requests', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropForeign(['alumni_id']);

            $table->foreignId('admin_id')
                ->references('id')
                ->on('users');

            $table->foreignId('alumni_id')
                ->references('id')
                ->on('users');
        });
    }
};
