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
        Schema::create('designation_required_qualifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('designation_id');
            $table->foreign('designation_id')->references('id')->on('designations')->onDelete('cascade');
            $table->text('qualification');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designation_required_qualifications');
    }
};
