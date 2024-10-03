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
        Schema::create('vehicle_maintenances', function (Blueprint $table) {
            $table->id('vehicle_maintenance_id'); // Primary Key
            $table->unsignedBigInteger('vehicle_id'); // Foreign Key
            $table->unsignedBigInteger('driver_id'); // Foreign Key
            $table->unsignedBigInteger('manager_id'); // Foreign Key
            $table->unsignedBigInteger('authority_id'); // Foreign Key (for approve)
            $table->unsignedBigInteger('maintenance_problems_id'); // Maintenance Problems
            $table->text('maintenance_problems_other_details')->nullable(); // Other details of problems (optional)
            $table->float('maintenance_start_fuel_level'); // Start fuel level
            $table->float('maintenance_end_fuel_level'); // End fuel level
            $table->decimal('maintenance_amount', 10, 2); // Maintenance amount
            $table->string('maintenance_service_center_name'); // Service center name
            $table->integer('maintenance_start_km_reading_by_manager'); // Start KM reading by manager
            $table->integer('maintenance_end_km_reading_by_manager'); // End KM reading by manager
            $table->date('maintenance_start_date'); // Maintenance start date
            $table->time('maintenance_start_time'); // Maintenance start time
            $table->date('maintenance_end_date')->nullable(); // Maintenance end date
            $table->time('maintenance_end_time')->nullable(); // Maintenance end time
            $table->string('maintenance_service_center_recept_file')->nullable(); // Service center receipt file (optional)
            $table->string('maintenance_approve_status'); // Approve status
            $table->string('maintenance_status');
            $table->string('exp_amt'); // Maintenance status
            $table->unsignedBigInteger('entry_by'); // Entry user ID (e.g., who created the record)
    
            $table->timestamps(); // Created at and Updated at timestamps
    
            // Foreign Key Constraints (assuming `vehicles`, `drivers`, `managers`, `admins`, and `users` tables exist)
            $table->foreign('vehicle_id')->references('vehicle_id')->on('vehicles')->onDelete('cascade');
            $table->foreign('driver_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('manager_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('admin_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('maintenance_problems_id')->references('vehicle_problems_id')->on('vehicle_problems')->onDelete('cascade');
            $table->foreign('entry_by')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_maintenances');
    }
};
