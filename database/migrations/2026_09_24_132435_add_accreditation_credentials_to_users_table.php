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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'certificate_file')) {
                $table->string('certificate_file')->nullable()->after('certificate_proof');
            }
            if (!Schema::hasColumn('users', 'valid_id_proof')) {
                $table->string('valid_id_proof')->nullable()->after('certificate_file');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'valid_id_proof')) {
                $table->dropColumn('valid_id_proof');
            }
            if (Schema::hasColumn('users', 'certificate_file')) {
                $table->dropColumn('certificate_file');
            }
        });
    }
};
