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
        Schema::create('aper_form_junior_hod_recommend', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->unsignedBigInteger('aper_form_id');
            $table->foreign('aper_form_id')->references('id')->on('aper_forms')->onDelete('cascade');
            $table->unsignedBigInteger('staff_id');
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            $table->unsignedBigInteger('promotion_salary_level_id')->nullable();
            $table->foreign('promotion_salary_level_id')->references('id')->on('salary_levels')->onDelete('cascade');
            $table->unsignedBigInteger('salary_increment_id')->nullable();
            $table->foreign('salary_increment_id')->references('id')->on('salary_steps')->onDelete('cascade');
            $table->text('no_salary_increment');
            $table->unsignedBigInteger('hod');
            $table->foreign('hod')->references('id')->on('staff')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aper_form_junior_h_o_d_recommendations');
    }
};
