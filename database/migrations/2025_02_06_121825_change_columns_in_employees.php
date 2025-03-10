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
        Schema::table('employees', function (Blueprint $table) {
            // Rename 'position' to 'position_id' and set it as a foreign key
            $table->unsignedBigInteger('position_id')->after('pin')->nullable();
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {

            $table->dropForeign(['position_id']);
            $table->dropColumn('position_id');
            $table->dropColumn('department_ids');
        });
    }
};
