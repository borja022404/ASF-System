<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('report_status_histories', function (Blueprint $table) {
            $table->id();
            
            // This is the correct way to link the report
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            
            // This is the corrected way to link the user
            $table->foreignId('updated_by_user_id')
                  ->nullable() // Allow null if the user is deleted
                  ->constrained('users')
                  ->onDelete('set null'); // Set the key to null if the user is deleted

            // Store the previous status
            $table->string('old_status')->nullable();
            
            // Store the new status
            $table->string('new_status');
            
            // Timestamps for when the change was logged
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('report_status_histories');
    }
};