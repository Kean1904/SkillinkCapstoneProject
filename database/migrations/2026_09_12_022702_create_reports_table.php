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
        Schema::create('reports', function (Blueprint $table) {
            $table->id('report_id');
            $table->string('covered_period');
            $table->integer('total_clients')->default(0);
            $table->integer('total_workers')->default(0);
            $table->integer('total_requests')->default(0);
            $table->integer('total_bookings')->default(0);
            $table->integer('total_peso_jobs')->default(0);
            $table->integer('placements')->default(0);
            $table->json('other_indicators')->nullable();
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
        Schema::dropIfExists('reports');
    }
};
