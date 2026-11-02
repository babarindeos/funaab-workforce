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
        // Guard: column may already exist (created in the base migration or added manually on live).
        if (Schema::hasColumn('salary_levels', 'uuid')) {
            return;
        }

        Schema::table('salary_levels', function (Blueprint $table) {
            //
            $table->uuid('uuid')->after('id')->unique()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: the column belongs to the base create_* migration.
    }
};
