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
        Schema::create('assigned_roles', function (Blueprint $table) {
            $table->id('assigned_role_id'); // Primary Key
            $table->unsignedBigInteger('user_id'); // Assuming user_id is an unsigned big integer
            $table->unsignedBigInteger('role_id'); // Match the type with the `role_id` in `userroles`
            $table->unsignedBigInteger('entry_by'); // Assuming entry_by is an unsigned big integer
            $table->timestamps();
    
            $table->foreign('role_id')->references('role_id')->on('userroles')->onDelete('cascade');
            $table->foreign('entry_by')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assigned_roles');
    }
};
