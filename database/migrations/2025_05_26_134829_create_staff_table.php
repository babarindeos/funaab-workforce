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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('staff_no');
            $table->unsignedBigInteger('staff_title_id');
            $table->foreign('staff_title_id')->references('id')->on('staff_titles')->onDelete('cascade');
            $table->string('surname');
            $table->string('firstname');
            $table->string('othernames')->nullable();
            $table->date('dob');
            $table->unsignedBigInteger('gender_id');
            $table->foreign('gender_id')->references('id')->on('genders')->onDelete('cascade');
            $table->unsignedBigInteger('lga_id');
            $table->foreign('lga_id')->references('id')->on('lgas')->onDelete('cascade');
            $table->unsignedBigInteger('senatorial_district_id')->nullable();
            $table->foreign('senatorial_district_id')->references('id')->on('senatorial_districts')->onDelete('cascade');
            $table->unsignedBigInteger('marital_status_id');
            $table->foreign('marital_status_id')->references('id')->on('marital_statuses')->onDelete('cascade');
            $table->unsignedBigInteger('staff_status_id');
            $table->foreign('staff_status_id')->references('id')->on('staff_statuses')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
