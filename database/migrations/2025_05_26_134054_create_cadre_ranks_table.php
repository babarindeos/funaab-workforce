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
        Schema::create('cadre_ranks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cadre_id');
            $table->foreign('cadre_id')->references('id')->on('cadres')->onDelete('cascade');
            $table->string('cadre_rank_name')->unique();            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cadre_ranks');
    }
};
