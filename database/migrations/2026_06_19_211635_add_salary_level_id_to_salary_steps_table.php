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
        Schema::table('salary_steps', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('salary_level_id')->after('id')->nullable();
            $table->foreign('salary_level_id')->references('id')->on('salary_levels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_steps', function (Blueprint $table) {
            //
            $table->dropForeign(['salary_level_id']);
            $table->dropColumn('salary_level_id');
        });
    }
};
