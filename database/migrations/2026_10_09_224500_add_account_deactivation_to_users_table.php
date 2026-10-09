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
            if (!Schema::hasColumn('users', 'deactivated_at')) {
                $table->timestamp('deactivated_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('users', 'deletion_scheduled_at')) {
                $table->timestamp('deletion_scheduled_at')->nullable()->after('deactivated_at');
            }
            if (!Schema::hasColumn('users', 'deactivation_reason')) {
                $table->text('deactivation_reason')->nullable()->after('deletion_scheduled_at');
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
            if (Schema::hasColumn('users', 'deactivation_reason')) {
                $table->dropColumn('deactivation_reason');
            }
            if (Schema::hasColumn('users', 'deletion_scheduled_at')) {
                $table->dropColumn('deletion_scheduled_at');
            }
            if (Schema::hasColumn('users', 'deactivated_at')) {
                $table->dropColumn('deactivated_at');
            }
        });
    }
};
