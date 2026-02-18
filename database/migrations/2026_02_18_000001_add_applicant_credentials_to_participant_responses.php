<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participant_responses', function (Blueprint $table) {
            $table->string('applicant_username')->nullable()->after('position');
            $table->string('applicant_password')->nullable()->after('applicant_username');
        });
    }

    public function down(): void
    {
        Schema::table('participant_responses', function (Blueprint $table) {
            $table->dropColumn(['applicant_username', 'applicant_password']);
        });
    }
};
