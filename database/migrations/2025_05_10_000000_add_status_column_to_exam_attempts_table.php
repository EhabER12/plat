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
        Schema::table('exam_attempts', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_attempts', 'status')) {
                $table->string('status')->default('in_progress')->after('answers')
                      ->comment('Status of the attempt: in_progress, completed, etc.');
            }
            
            if (!Schema::hasColumn('exam_attempts', 'time_spent_seconds')) {
                $table->integer('time_spent_seconds')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('exam_attempts', 'is_passed')) {
                $table->boolean('is_passed')->nullable()->after('time_spent_seconds');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            $columns = ['status', 'time_spent_seconds', 'is_passed'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('exam_attempts', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 