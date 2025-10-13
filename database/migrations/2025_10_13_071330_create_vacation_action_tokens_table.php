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
        Schema::create('vacation_action_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacation_id')->constrained('vacations')->onDelete('cascade');
            $table->string('token', 64)->unique();
            $table->enum('action', ['approve', 'reject']);
            $table->boolean('used')->default(false);
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['token', 'used']);
            $table->index(['vacation_id', 'action']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacation_action_tokens');
    }
};
