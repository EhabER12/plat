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
        if (!Schema::hasTable('parent_student_relations')) {
            Schema::create('parent_student_relations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->unsignedBigInteger('student_id')->nullable();
                $table->string('student_name');
                $table->string('relation_type')->default('parent'); // parent, guardian, other
                $table->string('birth_certificate')->nullable(); // document path
                $table->string('parent_id_card')->nullable(); // document path
                $table->string('additional_document')->nullable(); // document path
                $table->text('notes')->nullable();
                $table->string('verification_status')->default('pending'); // pending, approved, rejected
                $table->text('verification_notes')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->unsignedBigInteger('verified_by')->nullable();
                $table->timestamps();
                
                $table->foreign('parent_id')->references('user_id')->on('users')->onDelete('set null');
                $table->foreign('student_id')->references('user_id')->on('users')->onDelete('set null');
                $table->foreign('verified_by')->references('user_id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_student_relations');
    }
}; 