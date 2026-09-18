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
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id('request_id');
            $table->foreignId('client_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('peso_staff_id')->nullable()->constrained('users', 'user_id')->onDelete('set null');
            $table->string('category');
            $table->text('description')->nullable();
            $table->string('location_tag')->nullable();
            $table->string('preferred_schedule')->nullable();
            $table->date('date_posted');
            $table->string('status')->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_requests');
    }
};
