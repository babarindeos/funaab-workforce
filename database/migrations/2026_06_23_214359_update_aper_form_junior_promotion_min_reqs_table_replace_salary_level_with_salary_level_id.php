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
        Schema::table('aper_form_junior_promotion_min_reqs', function (Blueprint $table) {
            //Remove old column
            $table->dropColumn('grade_level');

            // Add new foreign key column
            $table->unsignedBigInteger('salary_level_id')->after('uuid');

            // Add foreign key constraint
            $table->foreign('salary_level_id')
                  ->references('id')
                  ->on('salary_levels')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aper_form_junior_promotion_min_reqs', function (Blueprint $table) {
            // Remove foreign key
            $table->dropForeign(['salary_level_id']);

            // Remove new column
            $table->dropColumn('salary_level_id');

            // Restore old column
            $table->string('grade_level');

        });
    }
};
