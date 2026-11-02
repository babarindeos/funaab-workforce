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
        if (Schema::hasColumn('employments', 'employment_type_id')) {
            return;
        }

        Schema::table('employments', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('employment_type_id')->nullable()->after('staff_category_id');
            $table->foreign('employment_type_id')->references('id')->on('employment_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: the column belongs to the base create_* migration.
    }
};
