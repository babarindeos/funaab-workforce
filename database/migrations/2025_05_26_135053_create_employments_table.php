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
        Schema::create('employments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            $table->unsignedBigInteger('staff_category_id');
            $table->foreign('staff_category_id')->references('id')->on('staff_categories')->onDelete('cascade');
            $table->string('ippis_no')->nullable();
            $table->unsignedBigInteger('division_id')->nullable();
            $table->foreign('division_id')->references('id')->on('divisions')->onDelete('cascade');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->unsignedBigInteger('highest_degree')->nullable();
            $table->foreign('highest_degree')->references('id')->on('degrees')->onDelete('cascade');
            $table->date('date_public_first_appointment')->nullable();
            $table->date('date_funaab_first_appointment')->nullable();
            $table->date('date_confirmation')->nullable();
            $table->unsignedBigInteger('designation_id')->nullable();
            $table->foreign('designation_id')->references('id')->on('designations')->onDelete('cascade');
            $table->date('date_present_appointment')->nullable();
            $table->unsignedBigInteger('salary_structure_id')->nullable();
            $table->foreign('salary_structure_id')->references('id')->on('salary_structures')->onDelete('cascade');
            $table->unsignedBigInteger('salary_step_id')->nullable();
            $table->foreign('salary_step_id')->references('id')->on('salary_steps')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employments');
    }
};
