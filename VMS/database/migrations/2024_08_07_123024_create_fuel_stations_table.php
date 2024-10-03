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
    Schema::create('fuel_stations', function (Blueprint $table) {
        $table->id('fuel_station_id');
        $table->string('fuel_station_name');
        $table->string('location');
        $table->unsignedBigInteger('entry_by');
        $table->timestamps();

        // Foreign key constraint (if needed)
        $table->foreign('entry_by')->references('user_id')->on('users')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_stations');
    }
};
