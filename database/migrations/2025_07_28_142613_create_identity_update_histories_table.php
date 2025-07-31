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
        Schema::create('identity_update_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('identity_id');
            $table->foreign('identity_id')->references('id')->on('identities')->onDelete('cascade');
            $table->unsignedBigInteger('update_field_id');
            $table->foreign('update_field_id')->references('id')->on('identity_update_fields')->onDelete('cascade');
            $table->string('old');
            $table->string('new');
            $table->text('remark');
            $table->unsignedBigInteger('updated_by');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('identity_update_histories');
    }
};
