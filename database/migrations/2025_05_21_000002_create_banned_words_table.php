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
        if (!Schema::hasTable('banned_words')) {
            Schema::create('banned_words', function (Blueprint $table) {
                $table->id();
                $table->string('word', 100);
                $table->string('type', 50)->default('general');
                $table->string('replacement', 100)->nullable();
                $table->tinyInteger('severity')->default(1);
                $table->boolean('active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();
                
                // Add index for faster lookups
                $table->index(['word', 'type', 'active']);
            });
            
            // Insert some default banned words
            $this->seedDefaultBannedWords();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banned_words');
    }
    
    /**
     * Seed default banned words.
     */
    private function seedDefaultBannedWords(): void
    {
        $bannedWords = [
            // General contact information
            ['word' => 'whatsapp', 'type' => 'contact_info', 'severity' => 2, 'replacement' => '***'],
            ['word' => 'facebook', 'type' => 'contact_info', 'severity' => 2, 'replacement' => '***'],
            ['word' => 'instagram', 'type' => 'contact_info', 'severity' => 2, 'replacement' => '***'],
            ['word' => 'telegram', 'type' => 'contact_info', 'severity' => 2, 'replacement' => '***'],
            ['word' => 'gmail', 'type' => 'contact_info', 'severity' => 2, 'replacement' => '***'],
            ['word' => 'yahoo', 'type' => 'contact_info', 'severity' => 2, 'replacement' => '***'],
            ['word' => 'hotmail', 'type' => 'contact_info', 'severity' => 2, 'replacement' => '***'],
            ['word' => 'outlook', 'type' => 'contact_info', 'severity' => 2, 'replacement' => '***'],
            
            // Profanity (Arabic and English)
            ['word' => 'كلب', 'type' => 'profanity', 'severity' => 3, 'replacement' => '****'],
            ['word' => 'حمار', 'type' => 'profanity', 'severity' => 3, 'replacement' => '****'],
            ['word' => 'غبي', 'type' => 'profanity', 'severity' => 2, 'replacement' => '***'],
            ['word' => 'stupid', 'type' => 'profanity', 'severity' => 2, 'replacement' => '******'],
            ['word' => 'idiot', 'type' => 'profanity', 'severity' => 2, 'replacement' => '*****'],
            ['word' => 'fool', 'type' => 'profanity', 'severity' => 2, 'replacement' => '****'],
            
            // General banned words
            ['word' => 'خصم', 'type' => 'general', 'severity' => 1, 'replacement' => '***'],
            ['word' => 'كوبون', 'type' => 'general', 'severity' => 1, 'replacement' => '***'],
            ['word' => 'discount', 'type' => 'general', 'severity' => 1, 'replacement' => '********'],
            ['word' => 'coupon', 'type' => 'general', 'severity' => 1, 'replacement' => '******'],
        ];
        
        $table = DB::table('banned_words');
        foreach ($bannedWords as $word) {
            $table->insert(array_merge($word, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
};
