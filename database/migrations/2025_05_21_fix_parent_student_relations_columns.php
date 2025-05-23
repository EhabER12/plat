<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('parent_student_relations', function (Blueprint $table) {
            // Add verification_status column if it doesn't exist
            if (!Schema::hasColumn('parent_student_relations', 'verification_status')) {
                $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending')->after('student_id');
            }

            // Add relation_type column if it doesn't exist
            if (!Schema::hasColumn('parent_student_relations', 'relation_type')) {
                $table->string('relation_type')->default('parent')->after('student_id');
            }

            // Add missing columns
            if (!Schema::hasColumn('parent_student_relations', 'student_name')) {
                $table->string('student_name')->after('student_id');
            }

            if (!Schema::hasColumn('parent_student_relations', 'verification_notes')) {
                $table->text('verification_notes')->nullable()->after('verification_status');
            }

            if (!Schema::hasColumn('parent_student_relations', 'birth_certificate')) {
                $table->string('birth_certificate')->nullable()->after('verification_notes');
            }

            if (!Schema::hasColumn('parent_student_relations', 'parent_id_card')) {
                $table->string('parent_id_card')->nullable()->after('birth_certificate');
            }

            if (!Schema::hasColumn('parent_student_relations', 'additional_document')) {
                $table->string('additional_document')->nullable()->after('parent_id_card');
            }

            if (!Schema::hasColumn('parent_student_relations', 'notes')) {
                $table->text('notes')->nullable()->after('additional_document');
            }

            if (!Schema::hasColumn('parent_student_relations', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verified_by');
            }
        });

        // Copy data from status to verification_status if status column exists
        if (Schema::hasColumn('parent_student_relations', 'status')) {
            DB::statement("UPDATE parent_student_relations SET verification_status = status WHERE verification_status IS NULL");
        }

        // Copy data from relationship to relation_type if relationship column exists
        if (Schema::hasColumn('parent_student_relations', 'relationship')) {
            DB::statement("UPDATE parent_student_relations SET relation_type = relationship WHERE relation_type IS NULL OR relation_type = 'parent'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parent_student_relations', function (Blueprint $table) {
            // Reverse the column renames
            if (Schema::hasColumn('parent_student_relations', 'verification_status')) {
                $table->renameColumn('verification_status', 'status');
            }

            if (Schema::hasColumn('parent_student_relations', 'relation_type')) {
                $table->renameColumn('relation_type', 'relationship');
            }

            // Drop added columns
            $columnsToRemove = [
                'student_name', 'verification_notes', 'birth_certificate',
                'parent_id_card', 'additional_document', 'notes', 'verified_at'
            ];

            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('parent_student_relations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
