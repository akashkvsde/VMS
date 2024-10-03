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
        Schema::create('login_credentials', function (Blueprint $table) {
            $table->id('login_credential_id'); // Primary Key
            $table->unsignedBigInteger('user_id'); // Foreign Key
            $table->string('login_id'); // Foreign Key
            $table->string('user_password'); // User Password
            $table->boolean('is_active')->default(true); // Active status
            $table->unsignedBigInteger('entry_by'); // Entry user ID (e.g., who created the record)
    
            $table->timestamps(); // Created at and Updated at timestamps
    
            // Foreign Key Constraints (assuming `users` table exists)
            $table->foreign('entry_by')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_credentials');
    }
};
