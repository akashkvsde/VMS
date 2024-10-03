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
        Schema::create('assigned_navigations', function (Blueprint $table) {
            $table->id('assign_nav_id'); // Primary Key
            $table->unsignedBigInteger('nav_id'); // Foreign Key for Navigation Items
            $table->unsignedBigInteger('role_id'); // Foreign Key for Roles
            $table->unsignedBigInteger('entry_by'); // Entry user ID (e.g., who created the record)
    
            $table->timestamps(); // Created at and Updated at timestamps
    
            // Foreign Key Constraints (assuming `navigation_items`, `roles`, and `users` tables exist)
            $table->foreign('nav_id')->references('nav_id')->on('navigations')->onDelete('cascade');
            $table->foreign('role_id')->references('role_id')->on('userroles')->onDelete('cascade');
            $table->foreign('entry_by')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assigned_navigations');
    }
};
