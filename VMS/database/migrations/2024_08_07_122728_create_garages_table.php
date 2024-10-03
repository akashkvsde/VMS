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
        Schema::create('garages', function (Blueprint $table) {
            $table->id('garage_id');
            $table->string('garage_name');
            $table->string('garage_owner');
            $table->string('location');
            $table->string('contact_person');
            $table->string('contact_no');
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
        Schema::dropIfExists('garages');
    }
};
