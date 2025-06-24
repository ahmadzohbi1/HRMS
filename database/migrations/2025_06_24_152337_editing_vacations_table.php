<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vacations', function (Blueprint $table) {
            $table->string('applicant_name')->nullable()->after('status');
            $table->string('applicant_phone')->nullable()->after('applicant_name');
            $table->string('applicant_email')->nullable()->after('applicant_phone');
            $table->text('reason')->nullable()->after('applicant_email');
        });
    }

    public function down(): void
    {
        Schema::table('vacations', function (Blueprint $table) {
            $table->dropColumn(['applicant_name', 'applicant_phone', 'applicant_email', 'reason']);
        });
    }
};