<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Use binary() which maps to BLOB; if you need LONGBLOB adjust with raw SQL
            $table->binary('image_data')->nullable()->after('image');
            $table->string('image_mime')->nullable()->after('image_data');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['image_data', 'image_mime']);
        });
    }
};
