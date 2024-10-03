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
        Schema::table('fuel_expenses', function (Blueprint $table) {
            $table->unique(['vehicle_id', 'driver_id', 'filling_date', 'last_km_reading'], 'vehicle_driver_date_km_unique'); // Shortened name
        });
    }
    
    public function down(): void
    {
        Schema::table('fuel_expenses', function (Blueprint $table) {
            $table->dropUnique('vehicle_driver_date_km_unique'); // Use the same name
        });
    }
};
