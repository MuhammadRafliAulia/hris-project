<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sub_tests', function (Blueprint $table) {
            $table->string('type')->default('default')->after('description');
            $table->json('kraepelin_config')->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('sub_tests', function (Blueprint $table) {
            $table->dropColumn(['type', 'kraepelin_config']);
        });
    }
};
