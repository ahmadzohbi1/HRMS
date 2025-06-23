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
        Schema::table('salaries', function (Blueprint $table) {
            // Add end_date to track when this salary version ends
            $table->date('end_date')->nullable()->after('effective_date');
            
            // Add version number to track salary history
            $table->integer('version')->default(1)->after('employee_id');
            
            // Add is_current flag for easier querying of current salary
            $table->boolean('is_current')->default(true)->after('status');
            
            // Add created_by for audit trail (optional)
            $table->unsignedBigInteger('created_by')->nullable()->after('notes');
            
            // Update indexes for better performance with versioning
            $table->index(['employee_id', 'effective_date']);
            $table->index(['employee_id', 'is_current']);
            $table->index(['employee_id', 'version']);
            
            // Add foreign key for created_by if you have users table
            // $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            // Drop foreign key first if it exists
            // $table->dropForeign(['created_by']);
            
            // Drop indexes
            $table->dropIndex(['employee_id', 'effective_date']);
            $table->dropIndex(['employee_id', 'is_current']);
            $table->dropIndex(['employee_id', 'version']);
            
            // Drop columns
            $table->dropColumn(['end_date', 'version', 'is_current', 'created_by']);
        });
    }
};