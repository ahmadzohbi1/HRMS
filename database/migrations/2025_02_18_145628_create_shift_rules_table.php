<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        // Create shift_rules table
        Schema::create('shift_rules', function (Blueprint $table) {
            $table->id();
            $table->string('shift_title'); // Shift title
            $table->integer('time_in_apply'); // Minutes after shift start
            $table->integer('time_out_apply'); // Minutes after shift end
            $table->boolean('deduct_hours')->nullable(); // 1 for Yes, NULL for No
            $table->integer('day_hours_deduction')->nullable(); // Minutes after shift start
            $table->boolean('give_warning')->nullable(); // 1 for Yes, NULL for No
            $table->string('warning_description')->nullable(); // Shift title
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       
        Schema::dropIfExists('shift_rules'); // Then drop shift_rules table
    }
};
