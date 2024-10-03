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
        Schema::create('vehicle_pollutions', function (Blueprint $table) {
            $table->id('vehicle_pollution_id'); // Primary Key
            $table->unsignedBigInteger('vehicle_id'); // Foreign Key
            $table->string('vehicle_pollution_puc_no'); // Pollution Under Control (PUC) Number
            $table->string('vehicle_pollution_puc_file')->nullable(); // PUC File (optional)
            $table->date('vehicle_pollution_start_date'); // PUC Start Date
            $table->date('vehicle_pollution_end_date'); // PUC End Date
            $table->unsignedBigInteger('entry_by'); // Entry user ID (e.g., who created the record)
    
            $table->timestamps(); // Created at and Updated at timestamps
    
            // Foreign Key Constraints (assuming `vehicles` and `users` tables exist)
            $table->foreign('vehicle_id')->references('vehicle_id')->on('vehicles')->onDelete('cascade');
            $table->foreign('entry_by')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_polutions');
    }
};
