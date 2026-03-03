<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warning_letters', function (Blueprint $table) {
            $table->string('tempat')->nullable()->after('paragraf_kedua');
            $table->string('proses')->nullable()->after('tempat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warning_letters', function (Blueprint $table) {
            $table->dropColumn(['tempat', 'proses']);
        });
    }
};
