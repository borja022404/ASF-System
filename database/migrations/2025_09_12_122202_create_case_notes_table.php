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
        Schema::create('case_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            // Link to the user (vet or admin) who wrote the note.
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->text('content');
            // Distinguish the type of note.
            $table->enum('note_type', ['vet_diagnosis', 'admin_review', 'farmer_comment'])->default('farmer_comment');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_notes');
    }
};