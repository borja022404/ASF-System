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
        Schema::create('text_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id');   // ID of the sender
            $table->enum('sender_role', ['vet', 'admin', 'farmer']); // Role of sender
            $table->unsignedBigInteger('receiver_id'); // ID of the receiver
            $table->enum('receiver_role', ['vet', 'admin', 'farmer']); // Role of receiver
            $table->text('message'); // The text message
            $table->boolean('is_read')->default(false); // Message read status
            $table->timestamps();

            // Foreign keys (optional, assuming you have a users table)
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('text_messages');
    }
};
