<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBonusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('salary_id');
            $table->decimal('amount', 10, 2);
            $table->date('bonus_date');
            $table->enum('type', ['performance', 'annual', 'project', 'attendance', 'special', 'other'])->default('other');
            $table->string('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('salary_id')->references('id')->on('salaries')->onDelete('cascade');
            
            // Indexes
            $table->index('salary_id');
            $table->index(['salary_id', 'status']);
            $table->index('bonus_date');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonuses');
    }
}