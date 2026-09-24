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
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id('log_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('actor_name', 100);
                $table->string('actor_role', 50)->default('System');
                $table->string('action', 100);
                $table->text('details')->nullable();
                $table->string('ip_address', 45)->nullable()->default('127.0.0.1');
                $table->string('status', 30)->default('Success');
                $table->timestamps();
            });

            // Seed initial audit trail logs from existing system data
            $now = now();
            $initialLogs = [
                [
                    'user_id' => 1,
                    'actor_name' => 'admin@Admin',
                    'actor_role' => 'Admin',
                    'action' => 'ADMIN_INSPECTION',
                    'details' => 'Administrative Audit Inspection and System Diagnostics performed.',
                    'ip_address' => '127.0.0.1',
                    'status' => 'Success',
                    'created_at' => $now->copy()->subMinutes(10),
                    'updated_at' => $now->copy()->subMinutes(10),
                ],
                [
                    'user_id' => 2,
                    'actor_name' => 'peso_officer@Staff',
                    'actor_role' => 'PESO Staff',
                    'action' => 'ACCREDITATION_REVIEW',
                    'details' => 'Reviewed worker credentials and verified TESDA certification authenticity.',
                    'ip_address' => '127.0.0.1',
                    'status' => 'Success',
                    'created_at' => $now->copy()->subMinutes(35),
                    'updated_at' => $now->copy()->subMinutes(35),
                ],
                [
                    'user_id' => 3,
                    'actor_name' => 'juan_plumber',
                    'actor_role' => 'Skilled Worker',
                    'action' => 'CREDENTIALS_SUBMISSION',
                    'details' => 'Submitted TESDA NC II Plumbing Certificate and Valid ID proof for PESO Accreditation.',
                    'ip_address' => '127.0.0.1',
                    'status' => 'Success',
                    'created_at' => $now->copy()->subHours(1)->subMinutes(15),
                    'updated_at' => $now->copy()->subHours(1)->subMinutes(15),
                ],
                [
                    'user_id' => 4,
                    'actor_name' => 'Testing 1',
                    'actor_role' => 'Residential',
                    'action' => 'BOOKING_CREATED',
                    'details' => 'Created direct service booking request with fixed rate and schedule calendar.',
                    'ip_address' => '127.0.0.1',
                    'status' => 'Success',
                    'created_at' => $now->copy()->subHours(2),
                    'updated_at' => $now->copy()->subHours(2),
                ],
                [
                    'user_id' => 5,
                    'actor_name' => 'maria_residential',
                    'actor_role' => 'Residential',
                    'action' => 'USER_LOGIN',
                    'details' => 'User logged into Skillink web portal from Magalang terminal.',
                    'ip_address' => '127.0.0.1',
                    'status' => 'Success',
                    'created_at' => $now->copy()->subHours(3),
                    'updated_at' => $now->copy()->subHours(3),
                ]
            ];

            DB::table('audit_logs')->insert($initialLogs);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
};
