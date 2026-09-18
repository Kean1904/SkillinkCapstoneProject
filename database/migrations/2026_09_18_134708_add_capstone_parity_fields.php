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
        // 1. Users Table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'age')) {
                $table->integer('age')->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender')->nullable()->after('age');
            }
            if (!Schema::hasColumn('users', 'barangay')) {
                $table->string('barangay')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'skills')) {
                $table->text('skills')->nullable()->after('location_tag');
            }
            if (!Schema::hasColumn('users', 'certificate_proof')) {
                $table->string('certificate_proof')->nullable()->after('skills');
            }
            if (!Schema::hasColumn('users', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('certificate_proof');
            }
            if (!Schema::hasColumn('users', 'rating')) {
                $table->decimal('rating', 3, 2)->default(5.00)->after('is_verified');
            }
            if (!Schema::hasColumn('users', 'profile_image_uri')) {
                $table->string('profile_image_uri')->nullable()->after('rating');
            }
        });

        // 2. Service Requests (Jobs) Table
        Schema::table('service_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'title')) {
                $table->string('title')->nullable()->after('request_id');
            }
            if (!Schema::hasColumn('service_requests', 'barangay')) {
                $table->string('barangay')->nullable()->after('location_tag');
            }
            if (!Schema::hasColumn('service_requests', 'posted_by')) {
                $table->string('posted_by')->nullable()->after('client_id');
            }
            if (!Schema::hasColumn('service_requests', 'applicant_username')) {
                $table->string('applicant_username')->nullable()->after('peso_staff_id');
            }
        });

        // 3. Bookings Table
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'booking_reference')) {
                $table->string('booking_reference')->nullable()->after('booking_id');
            }
            if (!Schema::hasColumn('bookings', 'client_username')) {
                $table->string('client_username')->nullable()->after('request_id');
            }
            if (!Schema::hasColumn('bookings', 'worker_username')) {
                $table->string('worker_username')->nullable()->after('client_username');
            }
            if (!Schema::hasColumn('bookings', 'client_name')) {
                $table->string('client_name')->nullable()->after('worker_username');
            }
            if (!Schema::hasColumn('bookings', 'worker_name')) {
                $table->string('worker_name')->nullable()->after('client_name');
            }
            if (!Schema::hasColumn('bookings', 'service_category')) {
                $table->string('service_category')->nullable()->after('worker_name');
            }
            if (!Schema::hasColumn('bookings', 'task_description')) {
                $table->text('task_description')->nullable()->after('service_category');
            }
            if (!Schema::hasColumn('bookings', 'service_address')) {
                $table->string('service_address')->nullable()->after('task_description');
            }
            if (!Schema::hasColumn('bookings', 'barangay')) {
                $table->string('barangay')->nullable()->after('service_address');
            }
            if (!Schema::hasColumn('bookings', 'estimated_budget')) {
                $table->string('estimated_budget')->nullable()->after('barangay');
            }
            if (!Schema::hasColumn('bookings', 'scheduled_date')) {
                $table->string('scheduled_date')->nullable()->after('estimated_budget');
            }
        });
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE bookings MODIFY status VARCHAR(50) NOT NULL DEFAULT 'PENDING'");

        // 4. Complaints Table
        Schema::table('complaints', function (Blueprint $table) {
            if (!Schema::hasColumn('complaints', 'complainant_username')) {
                $table->string('complainant_username')->nullable()->after('submitted_by');
            }
            if (!Schema::hasColumn('complaints', 'respondent_username')) {
                $table->string('respondent_username')->nullable()->after('complainant_username');
            }
        });

        // 5. Rating Reviews Table
        Schema::table('rating_reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('rating_reviews', 'client_username')) {
                $table->string('client_username')->nullable()->after('client_id');
            }
            if (!Schema::hasColumn('rating_reviews', 'worker_username')) {
                $table->string('worker_username')->nullable()->after('worker_id');
            }
        });
    }

    public function down()
    {
        // Rollback columns if needed
    }
};
