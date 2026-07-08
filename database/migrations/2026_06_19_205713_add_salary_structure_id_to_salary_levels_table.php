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
        Schema::table('salary_levels', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('salary_structure_id')->after('uuid')->nullable();
            $table->foreign('salary_structure_id')->references('id')->on('salary_structures')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_levels', function (Blueprint $table) {
            //
            $table->dropForeign(['salary_structure_id']);
            $table->dropColumn('salary_structure_id');
        });
    }
};
