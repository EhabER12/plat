<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BannedWord;
use Illuminate\Support\Facades\DB;

class BannedWordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing banned words
        DB::table('banned_words')->truncate();
        
        // Define banned words
        $bannedWords = [
            // Contact information
            ['word' => 'whatsapp', 'type' => 'contact_info', 'severity' => 2, 'replacement' => '***', 'active' => true],
            ['word' => 'facebook', 'type' => 'contact_info', 'severity' => 2, 'replacement' => '***', 'active' => true],
            ['word' => 'instagram', 'type' => 'contact_info', 'severity' => 2, 'replacement' => '***', 'active' => true],
            ['word' => 'telegram', 'type' => 'contact_info', 'severity' => 2, 'replacement' => '***', 'active' => true],
            ['word' => 'gmail', 'type' => 'contact_info', 'severity' => 2, 'replacement' => '***', 'active' => true],
            ['word' => 'yahoo', 'type' => 'contact_info', 'severity' => 2, 'replacement' => '***', 'active' => true],
            ['word' => 'hotmail', 'type' => 'contact_info', 'severity' => 2, 'replacement' => '***', 'active' => true],
            ['word' => 'outlook', 'type' => 'contact_info', 'severity' => 2, 'replacement' => '***', 'active' => true],
            
            // Arabic profanity
            ['word' => 'كلب', 'type' => 'profanity', 'severity' => 3, 'replacement' => '****', 'active' => true],
            ['word' => 'حمار', 'type' => 'profanity', 'severity' => 3, 'replacement' => '****', 'active' => true],
            ['word' => 'غبي', 'type' => 'profanity', 'severity' => 2, 'replacement' => '***', 'active' => true],
            
            // English profanity
            ['word' => 'stupid', 'type' => 'profanity', 'severity' => 2, 'replacement' => '******', 'active' => true],
            ['word' => 'idiot', 'type' => 'profanity', 'severity' => 2, 'replacement' => '*****', 'active' => true],
            ['word' => 'fool', 'type' => 'profanity', 'severity' => 2, 'replacement' => '****', 'active' => true],
            
            // General banned words
            ['word' => 'خصم', 'type' => 'general', 'severity' => 1, 'replacement' => '***', 'active' => true],
            ['word' => 'كوبون', 'type' => 'general', 'severity' => 1, 'replacement' => '***', 'active' => true],
            ['word' => 'discount', 'type' => 'general', 'severity' => 1, 'replacement' => '********', 'active' => true],
            ['word' => 'coupon', 'type' => 'general', 'severity' => 1, 'replacement' => '******', 'active' => true],
            
            // Test words (for easy testing)
            ['word' => 'test', 'type' => 'general', 'severity' => 1, 'replacement' => '****', 'active' => true],
            ['word' => 'banned', 'type' => 'general', 'severity' => 1, 'replacement' => '******', 'active' => true],
            ['word' => 'محظور', 'type' => 'general', 'severity' => 1, 'replacement' => '******', 'active' => true],
        ];
        
        // Insert banned words
        foreach ($bannedWords as $word) {
            BannedWord::create($word);
        }
    }
}
