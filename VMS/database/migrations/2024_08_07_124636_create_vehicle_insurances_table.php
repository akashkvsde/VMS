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
        Schema::create('vehicle_insurances', function (Blueprint $table) {
            $table->id('vehicle_insurance_id'); // Primary Key
            $table->unsignedBigInteger('vehicle_id'); // Foreign Key
            $table->string('insurance_company_name'); // Insurance Company Name
            $table->string('vehicle_insurance_agent_name'); // Insurance Agent Name
            $table->string('vehicle_insurance_agent_mobile_no'); // Insurance Agent Mobile Number
            $table->string('vehicle_insurance_no'); // Insurance Number
            $table->string('vehicle_insurance_file')->nullable(); // Insurance File (optional)
            $table->date('vehicle_insurance_start_date'); // Insurance Start Date
            $table->date('vehicle_insurance_end_date'); // Insurance End Date
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
        Schema::dropIfExists('vehicle_insurances');
    }
};
