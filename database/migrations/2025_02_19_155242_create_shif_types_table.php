<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('shift_name');
            $table->time('time_in');
            $table->time('time_out');
            $table->enum('shift_type', ['employee', 'department', 'company']); // Type of shift
            $table->foreignId('employee_id')->nullable()->constrained('employees')->onDelete('set null'); // Employee relationship
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null'); // Department relationship
            $table->boolean('company_shift')->default(false); // For company-wide shifts
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('shifts');
    }
};

