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
        Schema::create('aper_form_senior_personals', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->unsignedBigInteger('aper_form_id');
            $table->foreign('aper_form_id')->references('id')->on('aper_forms')->onDelete('cascade');
            $table->unsignedBigInteger('staff_id');
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            $table->string('surname');
            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->date('dob');
            $table->date('retirement_date');
            $table->unsignedBigInteger('marital_status_id');
            $table->foreign('marital_status_id')->references('id')->on('marital_statuses')->onDelete('cascade');
            $table->string('children');
            $table->string('telephone_no');
            $table->string('email_address');
            $table->unsignedBigInteger('division_id');
            $table->foreign('division_id')->references('id')->on('divisions')->onDelete('cascade');
            $table->unsignedBigInteger('department_id');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->date('date_first_appointment')->nullable();
            $table->string('post_first_appointment')->nullable();
            $table->date('date_last_appointment')->nullable();
            $table->string('post_last_appointment')->nullable();
            $table->date('date_current_appointment')->nullable();
            $table->string('post_current_appointment')->nullable();
            $table->boolean('appointment_confirmed')->default(false);
            $table->date('confirmation_date')->nullable();
            $table->string('present_grade_level_salary')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aper_form_senior_personals');
    }
};
