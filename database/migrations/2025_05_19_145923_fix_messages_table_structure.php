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
        // Step 1: Check if messages table exists, if not create it (unlikely to be used)
        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->bigIncrements('message_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('sender_id');
                $table->unsignedBigInteger('receiver_id');
                $table->text('content');
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->unsignedBigInteger('course_id')->nullable();
                $table->timestamps();
                
                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('set null');
                $table->foreign('sender_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->foreign('receiver_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('set null');
            });
        } else {
            // Step 2: Make modifications to existing table
            // First, check table structure
            $columns = Schema::getColumnListing('messages');
            
            // Add missing columns if they don't exist
            Schema::table('messages', function (Blueprint $table) use ($columns) {
                if (!in_array('user_id', $columns)) {
                    $table->unsignedBigInteger('user_id')->nullable();
                }
                
                if (!in_array('sender_id', $columns)) {
                    $table->unsignedBigInteger('sender_id')->nullable();
                }
                
                if (!in_array('receiver_id', $columns)) {
                    $table->unsignedBigInteger('receiver_id')->nullable();
                }
                
                if (!in_array('content', $columns)) {
                    $table->text('content')->nullable();
                }
                
                if (!in_array('is_read', $columns)) {
                    $table->boolean('is_read')->default(false);
                }
                
                if (!in_array('read_at', $columns)) {
                    $table->timestamp('read_at')->nullable();
                }
                
                if (!in_array('course_id', $columns)) {
                    $table->unsignedBigInteger('course_id')->nullable();
                }
                
                if (!in_array('created_at', $columns)) {
                    $table->timestamp('created_at')->nullable();
                }
                
                if (!in_array('updated_at', $columns)) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
            
            // Remove content filtering columns if they exist
            Schema::table('messages', function (Blueprint $table) use ($columns) {
                $dropCols = [];
                
                if (in_array('contains_flagged_content', $columns)) {
                    $dropCols[] = 'contains_flagged_content';
                }
                
                if (in_array('flagged_severity', $columns)) {
                    $dropCols[] = 'flagged_severity';
                }
                
                if (in_array('is_filtered', $columns)) {
                    $dropCols[] = 'is_filtered';
                }
                
                if (!empty($dropCols)) {
                    $table->dropColumn($dropCols);
                }
            });
            
            // Add foreign keys if not present
            try {
                Schema::table('messages', function (Blueprint $table) {
                    // Check and add foreign keys, but don't worry if they fail
                    try {
                        if (!$this->hasForeignKey('messages', 'user_id')) {
                            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('set null');
                        }
                    } catch (\Exception $e) {
                        // Log but continue
                        DB::statement('UPDATE messages SET user_id = NULL WHERE user_id NOT IN (SELECT user_id FROM users)');
                    }
                    
                    try {
                        if (!$this->hasForeignKey('messages', 'sender_id')) {
                            $table->foreign('sender_id')->references('user_id')->on('users')->onDelete('cascade');
                        }
                    } catch (\Exception $e) {
                        // Log but continue
                        DB::statement('UPDATE messages SET sender_id = NULL WHERE sender_id NOT IN (SELECT user_id FROM users)');
                    }
                    
                    try {
                        if (!$this->hasForeignKey('messages', 'receiver_id')) {
                            $table->foreign('receiver_id')->references('user_id')->on('users')->onDelete('cascade');
                        }
                    } catch (\Exception $e) {
                        // Log but continue
                        DB::statement('UPDATE messages SET receiver_id = NULL WHERE receiver_id NOT IN (SELECT user_id FROM users)');
                    }
                    
                    try {
                        if (!$this->hasForeignKey('messages', 'course_id')) {
                            $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('set null');
                        }
                    } catch (\Exception $e) {
                        // Log but continue
                        DB::statement('UPDATE messages SET course_id = NULL WHERE course_id NOT IN (SELECT course_id FROM courses)');
                    }
                });
            } catch (\Exception $e) {
                // Just log the error but don't fail the migration
                error_log('Error adding foreign keys to messages table: ' . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructive action in down() to avoid losing data
    }
    
    /**
     * Check if a foreign key constraint exists
     *
     * @param string $table
     * @param string $column
     * @return bool
     */
    private function hasForeignKey($table, $column)
    {
        try {
            $conn = Schema::getConnection();
            $dbSchemaManager = $conn->getDoctrineSchemaManager();
            $doctrineTable = $dbSchemaManager->listTableDetails($table);
            
            $foreignKey = $table . '_' . $column . '_foreign';
            return $doctrineTable->hasForeignKey($foreignKey);
        } catch (\Exception $e) {
            return false;
        }
    }
};
