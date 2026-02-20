<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        // Convert image_data from BLOB to LONGBLOB so it can store larger images (up to ~4GB)
        DB::statement('ALTER TABLE `questions` MODIFY `image_data` LONGBLOB NULL');
    }

    public function down(): void
    {
        // Revert to BLOB (smaller) if rolling back
        DB::statement('ALTER TABLE `questions` MODIFY `image_data` BLOB NULL');
    }
};
