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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Add a unique report identifier for better tracking.
            $table->string('report_id')->unique();
            
            // Use a specific field for pig health status.
            $table->enum('pig_health_status', ['unassessed','infected','dead', 'isolate'])->default('unassessed');
            $table->text('symptoms_description')->nullable();
            $table->date('symptom_onset_date')->nullable();
            $table->date('mortality_date')->nullable();
            $table->integer('affected_pig_count')->default(0);
            $table->enum('report_status', ['submitted', 'under_inspection', 'resolved', 'closed'])->default('submitted');
            $table->enum('risk_level', ['low', 'medium', 'high']);
            $table->string('location_name')->nullable();
            $table->string('barangay')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Use a more descriptive name for the "seen" status.
            $table->boolean('is_read_by_staff')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};