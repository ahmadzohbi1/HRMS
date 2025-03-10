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
        Schema::create('companies', function (Blueprint $table) {
            $table->id(); // This creates an unsignedBigInteger column
            $table->string('name');
            $table->string('username')->unique();
            $table->string('dash_pin');
            $table->timestamps();
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')->after('role_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('remember_token')->after('fcm_token');
        });
        Schema::table('employees', function (Blueprint $table) {
            $table->string('image_url')->after('blood_type')->nullable();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
        Schema::table('users', function (Blueprint $table) {
            // If you need to reverse this action, re-add the column
            $table->unsignedBigInteger('company_id')->nullable();
        });
        
    }
};
