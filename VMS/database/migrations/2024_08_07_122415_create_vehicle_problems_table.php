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
        Schema::create('vehicle_problems', function (Blueprint $table) {
            $table->id('vehicle_problems_id');
            $table->string('vehicle_problems_name');
            $table->unsignedBigInteger('entry_by');
            $table->timestamps();


            $table->foreign('entry_by')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_problems');
    }
};
