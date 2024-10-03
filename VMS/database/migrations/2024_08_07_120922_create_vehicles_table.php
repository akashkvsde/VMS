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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id('vehicle_id'); // Primary Key
            $table->unsignedBigInteger('vehicle_category_id'); // Foreign Key
            $table->unsignedBigInteger('vehicle_owner_id'); // Foreign Key
            $table->string('vehicle_name'); // Vehicle Name
            $table->string('vehicle_model'); // Vehicle Model
            $table->date('vehicle_purchase_date'); // Vehicle Purchase Date
            $table->string('vehicle_rc_no'); // RC Number
            $table->string('vehicle_rc_file')->nullable(); // RC File (optional)
            $table->string('vehicle_fastag_no')->nullable(); // FASTag Number (optional)
            $table->string('vehicle_rto_no')->nullable(); // RTO Number (optional)
            $table->date('vehicle_fitness_end')->nullable(); // Fitness End Date (optional)
            $table->string('vehicle_chassis_no'); // Chassis Number
            $table->string('vehicle_engine_no'); // Engine Number
            $table->string('vehicle_fuel_type'); // Fuel Type
            $table->boolean('is_active')->default(true); // Active status
            $table->unsignedBigInteger('entry_by'); // Entry user ID (e.g., who created the record)
    
            $table->timestamps(); // Created at and Updated at timestamps
    
            // Foreign Key Constraints (assuming `vehicle_categories` and `vehicle_owners` tables exist)
            $table->foreign('vehicle_category_id')->references('vehicle_category_id')->on('vehicles_categories')->onDelete('cascade');
            $table->foreign('vehicle_owner_id')->references('vehicle_owner_id')->on('vehicle_owners')->onDelete('cascade');
            $table->foreign('entry_by')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
