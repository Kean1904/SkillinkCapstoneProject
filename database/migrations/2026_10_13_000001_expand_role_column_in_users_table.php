<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Expand role column to VARCHAR(50) to prevent truncation errors in MySQL
        DB::statement("ALTER TABLE users MODIFY role VARCHAR(50) NOT NULL DEFAULT 'residential'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('skilled worker', 'residential', 'peso staff', 'admin') NOT NULL");
    }
};
