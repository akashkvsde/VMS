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
        Schema::create('vehicle_movements', function (Blueprint $table) {
            $table->id('vehicle_movement_id'); // Primary Key
            $table->unsignedBigInteger('vehicle_id'); // Foreign Key
            $table->unsignedBigInteger('driver_id'); // Foreign Key
            $table->unsignedBigInteger('manager_id'); // Foreign Key
            $table->string('movement_start_from'); // Start location
            $table->string('movement_destination'); // Destination
            $table->string('purpose_of_visit'); // Purpose of the visit
            $table->string('taken_by'); // Person who took the vehicle
            $table->date('movement_start_date'); // Start date of the movement
            $table->time('movement_start_time'); // Start time of the movement
            $table->date('movement_end_date')->nullable(); // End date of the movement (nullable)
            $table->time('movement_end_time')->nullable(); // End time of the movement (nullable)
            $table->integer('movement_start_km_reading_by_manager'); // Start KM reading by manager
            $table->integer('movement_end_km_reading_by_manager')->nullable(); // End KM reading by manager (nullable)
            $table->integer('movement_start_km_reading_by_driver'); // Start KM reading by driver
            $table->integer('movement_end_km_reading_by_driver')->nullable(); // End KM reading by driver (nullable)
            $table->float('movement_distance_covered')->nullable(); // Distance covered during movement (nullable)
            $table->float('movement_status')->default(true); // Distance covered during movement (nullable)
            $table->unsignedBigInteger('entry_by'); // Entry user ID (e.g., who created the record)
    
            $table->timestamps(); // Created at and Updated at timestamps
    
            // Foreign Key Constraints (assuming `vehicles`, `drivers`, `managers`, and `users` tables exist)
            $table->foreign('vehicle_id')->references('vehicle_id')->on('vehicles')->onDelete('cascade');
            $table->foreign('driver_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('manager_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('entry_by')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_movements');
    }
};
