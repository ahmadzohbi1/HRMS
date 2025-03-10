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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->unique();
            $table->string('address')->nullable();
            $table->date('date_of_birth'); 
            $table->string('pin', 4); 
            $table->string('gender')->nullable(); // Added gender
            $table->string('marital_status')->nullable(); // Added marital status
            $table->string('nationality')->nullable(); // Added nationality
            $table->string('place_of_birth')->nullable(); // Added place of birth
            $table->string('blood_type')->nullable(); // Added blood type
            $table->timestamps();
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
