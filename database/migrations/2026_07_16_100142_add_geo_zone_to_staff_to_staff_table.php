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
        // Guard: column may already exist (created in the base migration or added manually on live).
        if (Schema::hasColumn('staff', 'geo_zone_id')) {
            return;
        }

        Schema::table('staff', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('geo_zone_id')->after('senatorial_district_id')->nullable();
            $table->foreign('geo_zone_id')->references('id')->on('geo_pol_zones')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            //
            $table->dropForeign(['geo_zone_id']);
            $table->dropColumn('geo_zone_id');
        });
    }
};
