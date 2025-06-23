<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('salary_id');
            $table->decimal('amount', 10, 2);
            $table->date('advance_date');
            $table->string('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid', 'completed'])->default('pending');
            $table->date('deduction_start_date')->nullable();
            $table->integer('installments')->default(1);
            $table->decimal('remaining_amount', 10, 2)->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('salary_id')->references('id')->on('salaries')->onDelete('cascade');
            
            // Indexes
            $table->index('salary_id');
            $table->index(['salary_id', 'status']);
            $table->index('advance_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('advances');
    }
}