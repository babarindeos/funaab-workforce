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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('division_id');
            $table->foreign('division_id')->references('id')->on('divisions')->onDelete('cascade');
            $table->unsignedBigInteger('parent_department')->nullable();
            $table->foreign('parent_department')->references('id')->on('departments')->onDelete('cascade');
            $table->string('department_name')->unique();
            $table->string('short_name')->unique();
            $table->timestamps();

           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
