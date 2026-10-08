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
        Schema::table('complaints', function (Blueprint $table) {
            if (!Schema::hasColumn('complaints', 'evidence_files')) {
                $table->longText('evidence_files')->nullable()->after('description');
            }
            if (!Schema::hasColumn('complaints', 'other_category')) {
                $table->string('other_category', 255)->nullable()->after('complaint_type');
            }
            if (!Schema::hasColumn('complaints', 'resolution_decision')) {
                $table->string('resolution_decision', 150)->nullable()->after('resolution_notes');
            }
            if (!Schema::hasColumn('complaints', 'sanction_status')) {
                $table->string('sanction_status', 100)->nullable()->after('resolution_decision');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn(['evidence_files', 'other_category', 'resolution_decision', 'sanction_status']);
        });
    }
};
