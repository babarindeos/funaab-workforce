<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Staff documents held in Registry.
 *   document_categories  e.g. "Appointment Records"
 *   document_types       e.g. "Appointment Letter" (belongs to a category)
 *   staff_documents      the uploaded files (Registry uploads are approved
 *                        at once; staff uploads wait for Registry approval)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_categories', function (Blueprint $t) {
            $t->id();
            $t->uuid('uuid')->unique();
            $t->string('name')->unique();
            $t->string('description')->nullable();
            $t->unsignedSmallInteger('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('document_types', function (Blueprint $t) {
            $t->id();
            $t->uuid('uuid')->unique();
            $t->foreignId('document_category_id')->constrained('document_categories')->restrictOnDelete();
            $t->string('name');
            $t->string('description')->nullable();
            $t->unsignedSmallInteger('sort_order')->default(0);
            $t->timestamps();
            $t->unique(['document_category_id', 'name']);
        });

        Schema::create('staff_documents', function (Blueprint $t) {
            $t->id();
            $t->uuid('uuid')->unique();
            $t->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $t->foreignId('document_type_id')->constrained('document_types')->restrictOnDelete();
            $t->string('title')->nullable();            // e.g. "Promotion to Principal Systems Analyst"
            $t->date('document_date')->nullable();      // date on the letter / certificate
            $t->text('description')->nullable();

            $t->string('file_path');                    // private disk, never under public/
            $t->string('original_name');
            $t->string('mime_type', 100);
            $t->unsignedInteger('file_size');

            $t->string('source', 10);                   // registry | staff
            $t->string('status', 10)->default('pending'); // approved | pending | rejected
            $t->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('reviewed_at')->nullable();
            $t->text('rejection_reason')->nullable();

            $t->timestamps();
            $t->index(['staff_id', 'status']);
            $t->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_documents');
        Schema::dropIfExists('document_types');
        Schema::dropIfExists('document_categories');
    }
};
