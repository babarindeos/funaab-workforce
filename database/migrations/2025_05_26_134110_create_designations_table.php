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
        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cadre_rank_id')->unique();
            $table->foreign('cadre_rank_id')->references('id')->on('cadre_ranks')->onDelete('cascade');
            $table->unsignedBigInteger('salary_structure_id');
            $table->foreign('salary_structure_id')->references('id')->on('salary_structures')->onDelete('cascade');
            $table->unsignedBigInteger('salary_level_id');
            $table->foreign('salary_level_id')->references('id')->on('salary_levels')->onDelete('cascade');
            $table->unsignedBigInteger('next_cadre_rank')->nullable();
            $table->foreign('next_cadre_rank')->references('id')->on('cadre_ranks')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designations');
    }
};
