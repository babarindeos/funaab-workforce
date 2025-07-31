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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('title');
            $table->string('ippis_no');
            $table->string('dob');
            $table->string('gender');
            $table->string('phone');
            $table->string('avatar');
            $table->date('date_first_appointment_service');
            $table->date('date_first_appointment_funaab');
            $table->date('date_confirmation');
            $table->date('date_present_appointment');
            $table->string('designation');
            $table->string('salary_structure');
            $table->string('salary_level');
            $table->string('salary_level_step');
            $table->string('staff_status');
            $table->string('geopolitical_zone');
            $table->string('state');
            $table->string('lga');
            $table->string('senatorial_district');
            $table->string('remarks');
            $table->string('cadre');
            $table->string('change_name');
            $table->date('date_name_change');
            $table->string('reason_name_change');
            $table->string('original_names');
            $table->string('dob');
            $table->string('birth_place');
            $table->string('nationality');
            $table->string('permanent_home_address');
            $table->string('nok_fullnames');
            $table->string('nok_address');
            $table->string('nok_phone');
            $table->string('nok_email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
