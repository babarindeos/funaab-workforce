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
        Schema::create('identities', function (Blueprint $table) {
            $table->id();
            $table->string('fileno');
            $table->string('title');
            $table->string('names');
            $table->string('ippis_no')->nullable();
            $table->string('college')->nullable();
            $table->string('department')->nullable();
            $table->date('dob')->nullable();
            $table->string('qualifications')->nullable();
            $table->string('highest_qualification')->nullable();
            $table->char('gender', 1)->nullable();
            $table->date('date_first_appointment_public_service')->nullable();
            $table->date('date_first_appointment_funaab')->nullable();
            $table->date('date_confirmation')->nullable();
            $table->string('current_designation')->nullable();
            $table->date('date_present_appointment')->nullable();
            $table->string('salary_structure')->nullable();
            $table->string('salary_level')->nullable();
            $table->string('salary_level_step')->nullable();
            $table->string('staff_status')->nullable();
            $table->string('geo_political_zone')->nullable();
            $table->string('state')->nullable();
            $table->string('lga')->nullable();
            $table->string('senatorial_district')->nullable();
            $table->string('remarks')->nullable();
            $table->string('cadre')->nullable();
            $table->string('nok')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('identities');
    }
};
