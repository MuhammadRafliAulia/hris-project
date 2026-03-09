<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sub_tests', function (Blueprint $table) {
            $table->json('disc_config')->nullable()->after('kraepelin_config');
        });
    }

    public function down(): void
    {
        Schema::table('sub_tests', function (Blueprint $table) {
            $table->dropColumn('disc_config');
        });
    }
};
