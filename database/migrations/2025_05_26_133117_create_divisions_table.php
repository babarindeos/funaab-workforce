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
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('division_type_id');
            $table->foreign('division_type_id')->references('id')->on('division_types')->onDelete('cascade');
            $table->unsignedBigInteger('parent_division')->nullable();
            $table->foreign('parent_division')->references('id')->on('divisions')->onDelete('cascade');
            $table->string('division_name')->unique();
            $table->string('short_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('divisions');
    }
};
