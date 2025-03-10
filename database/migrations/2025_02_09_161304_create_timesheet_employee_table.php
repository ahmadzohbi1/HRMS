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
        Schema::create('timesheet_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->time('total_hours_month')->nullable();
            $table->string('month');
            $table->timestamps();
        });
        Schema::create('hour_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->unique('employee_id');
            $table->double('hour_rate');
            $table->string('currency');
            $table->timestamps();
        }); 
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('currency', 10); 
            $table->double('salary');
            $table->string('month');
            $table->timestamps();
        });
        Schema::create('warnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('warning_title');
            $table->string('warning_type');
            $table->longText('warning_decsription');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timesheet_employees');
        Schema::dropIfExists('hour_rates');
        Schema::dropIfExists('salaries');
        Schema::dropIfExists('warnings');
        
    }
};
