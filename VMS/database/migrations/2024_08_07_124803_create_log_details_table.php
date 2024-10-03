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
        Schema::create('log_details', function (Blueprint $table) {
            $table->id('log_detail_id'); // Primary Key
            $table->unsignedBigInteger('user_id'); // Foreign Key
            $table->unsignedBigInteger('login_ip'); // Foreign Key
            $table->string('application_activity'); // Foreign Key
            $table->date('date'); // Date of log entry
            $table->string('device'); // Foreign Key
            $table->string('browser'); // Foreign Key
            $table->string('location'); // Foreign Key
            $table->time('login_time')->nullable(); // Login time
            $table->time('logout_time')->nullable(); // Logout time
    
            $table->timestamps(); // Created at and Updated at timestamps
    
            // Foreign Key Constraint (assuming `users` table exists)
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_details');
    }
};
