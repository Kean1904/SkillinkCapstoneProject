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
        // 1. Add verification status and rejection/resubmission reason to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'verification_status')) {
                $table->string('verification_status', 50)->default('pending')->after('is_verified');
            }
            if (!Schema::hasColumn('users', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('verification_status');
            }
        });

        // 2. Create announcements table for PESO Municipal Broadcasts
        if (!Schema::hasTable('announcements')) {
            Schema::create('announcements', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('category', 100)->default('PESO Official Advisory');
                $table->text('message');
                $table->string('target_audience', 50)->default('all'); // 'all', 'skilled_worker', 'residential'
                $table->string('posted_by', 150)->default('PESO Magalang');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
            if (Schema::hasColumn('users', 'verification_status')) {
                $table->dropColumn('verification_status');
            }
        });

        Schema::dropIfExists('announcements');
    }
};
