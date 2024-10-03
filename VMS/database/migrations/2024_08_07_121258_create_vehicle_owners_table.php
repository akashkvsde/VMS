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
        Schema::create('vehicle_owners', function (Blueprint $table) {
            $table->id('vehicle_owner_id'); // Primary Key
            $table->string('vehicle_owner_name'); // Owner Name
            $table->unsignedBigInteger('organization_id'); // Foreign Key
            $table->string('vehicle_owner_mobile_no_1'); // Primary Mobile Number
            $table->string('vehicle_owner_mobile_no_2')->nullable(); // Secondary Mobile Number (optional)
            $table->unsignedBigInteger('entry_by'); // Entry user ID (e.g., who created the record)
    
            $table->timestamps(); // Created at and Updated at timestamps
    
            // Foreign Key Constraints (assuming `organizations` and `users` tables exist)
            $table->foreign('organization_id')->references('organization_id')->on('organizations')->onDelete('cascade');
            $table->foreign('entry_by')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_owners');
    }
};
