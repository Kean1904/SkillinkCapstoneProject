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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_id');
            $table->foreignId('request_id')->constrained('service_requests', 'request_id')->onDelete('cascade');
            $table->foreignId('worker_id')->constrained('worker_profiles', 'worker_id')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            $table->date('scheduled_at')->nullable();
            $table->date('completion_date')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
