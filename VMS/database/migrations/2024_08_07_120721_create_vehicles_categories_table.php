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
        Schema::create('vehicles_categories', function (Blueprint $table) {
            $table->id('vehicle_category_id'); // Primary Key
            $table->string('vehicle_category_name'); // Vehicle Category Name
            $table->unsignedBigInteger('entry_by'); // Entry user ID (e.g., who created the record)
    
            $table->timestamps(); // Created at and Updated at timestamps
    
            // Foreign Key Constraint (assuming `users` table exists)
            $table->foreign('entry_by')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles_categories');
    }
};
