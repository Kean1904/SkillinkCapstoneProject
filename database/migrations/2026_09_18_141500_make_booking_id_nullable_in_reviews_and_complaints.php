<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            DB::statement("ALTER TABLE rating_reviews MODIFY booking_id BIGINT UNSIGNED NULL;");
        } catch (\Throwable $e) {}

        try {
            DB::statement("ALTER TABLE complaints MODIFY booking_id BIGINT UNSIGNED NULL;");
        } catch (\Throwable $e) {}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Keep nullable
    }
};
