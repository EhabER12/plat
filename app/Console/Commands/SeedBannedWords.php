<?php

namespace App\Console\Commands;

use App\Models\BannedWord;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedBannedWords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'banned-words:seed {--force : Force seeding even if table is not empty}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the banned words table with initial values';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (BannedWord::count() > 0 && !$this->option('force')) {
            if (!$this->confirm('The banned_words table is not empty. Do you want to continue?')) {
                $this->info('Command aborted.');
                return;
            }
        }

        $this->info('Seeding banned words...');

        $this->seedContactInformation();
        $this->seedProfanityWords();
        $this->seedPlatformBypass();
        
        $this->info('Banned words seeded successfully!');
        $this->info('Total words: ' . BannedWord::count());
    }

    /**
     * Seed contact information patterns.
     */
    protected function seedContactInformation()
    {
        $this->info('Seeding contact information patterns...');

        $contactWords = [
            // Phone numbers
            ['word' => '01\d{9}', 'type' => 'contact_info', 'replacement' => '***Phone Number***', 'severity' => 3],
            ['word' => '00\d{10,14}', 'type' => 'contact_info', 'replacement' => '***Phone Number***', 'severity' => 3],
            ['word' => '\+\d{10,14}', 'type' => 'contact_info', 'replacement' => '***Phone Number***', 'severity' => 3],
            
            // Email domains
            ['word' => 'gmail.com', 'type' => 'contact_info', 'replacement' => '***Email***', 'severity' => 3],
            ['word' => 'yahoo.com', 'type' => 'contact_info', 'replacement' => '***Email***', 'severity' => 3],
            ['word' => 'hotmail.com', 'type' => 'contact_info', 'replacement' => '***Email***', 'severity' => 3],
            ['word' => 'outlook.com', 'type' => 'contact_info', 'replacement' => '***Email***', 'severity' => 3],
            
            // Social media
            ['word' => 'facebook.com', 'type' => 'contact_info', 'replacement' => '***Social Media***', 'severity' => 2],
            ['word' => 'fb.com', 'type' => 'contact_info', 'replacement' => '***Social Media***', 'severity' => 2],
            ['word' => 'facebook', 'type' => 'contact_info', 'replacement' => '***Social Media***', 'severity' => 2],
            ['word' => 'instagram.com', 'type' => 'contact_info', 'replacement' => '***Social Media***', 'severity' => 2],
            ['word' => 'instagram', 'type' => 'contact_info', 'replacement' => '***Social Media***', 'severity' => 2],
            ['word' => 'twitter.com', 'type' => 'contact_info', 'replacement' => '***Social Media***', 'severity' => 2],
            ['word' => 'twitter', 'type' => 'contact_info', 'replacement' => '***Social Media***', 'severity' => 2],
            ['word' => 'telegram', 'type' => 'contact_info', 'replacement' => '***Messaging App***', 'severity' => 3],
            ['word' => 'whatsapp', 'type' => 'contact_info', 'replacement' => '***Messaging App***', 'severity' => 3],
            ['word' => 'signal', 'type' => 'contact_info', 'replacement' => '***Messaging App***', 'severity' => 2],
            
            // URL patterns
            ['word' => 'https://', 'type' => 'contact_info', 'replacement' => '***External Link***', 'severity' => 2],
            ['word' => 'http://', 'type' => 'contact_info', 'replacement' => '***External Link***', 'severity' => 2],
            ['word' => 'www.', 'type' => 'contact_info', 'replacement' => '***External Link***', 'severity' => 2],
        ];
        
        foreach ($contactWords as $word) {
            BannedWord::updateOrCreate(
                ['word' => $word['word'], 'type' => $word['type']],
                [
                    'replacement' => $word['replacement'],
                    'severity' => $word['severity'],
                    'active' => true,
                    'notes' => 'Auto seeded by command'
                ]
            );
        }
        
        $this->info('Contact information patterns seeded: ' . count($contactWords) . ' items');
    }

    /**
     * Seed profanity words.
     */
    protected function seedProfanityWords()
    {
        $this->info('Seeding profanity words...');

        // Basic profanity list - Add more as needed
        $profanityWords = [
            ['word' => 'damn', 'type' => 'profanity', 'replacement' => '****', 'severity' => 1],
            ['word' => 'hell', 'type' => 'profanity', 'replacement' => '****', 'severity' => 1],
            ['word' => 'idiot', 'type' => 'profanity', 'replacement' => '*****', 'severity' => 2],
            ['word' => 'stupid', 'type' => 'profanity', 'replacement' => '******', 'severity' => 2],
            ['word' => 'dumb', 'type' => 'profanity', 'replacement' => '****', 'severity' => 2],
            ['word' => 'freak', 'type' => 'profanity', 'replacement' => '*****', 'severity' => 2],
            // Add more profanity words as needed
        ];
        
        foreach ($profanityWords as $word) {
            BannedWord::updateOrCreate(
                ['word' => $word['word'], 'type' => $word['type']],
                [
                    'replacement' => $word['replacement'],
                    'severity' => $word['severity'],
                    'active' => true,
                    'notes' => 'Auto seeded by command'
                ]
            );
        }
        
        $this->info('Profanity words seeded: ' . count($profanityWords) . ' items');
    }

    /**
     * Seed platform bypass words.
     */
    protected function seedPlatformBypass()
    {
        $this->info('Seeding platform bypass patterns...');

        $bypassWords = [
            ['word' => 'private lessons', 'type' => 'platform_bypass', 'replacement' => '***private tutoring***', 'severity' => 4],
            ['word' => 'teach outside', 'type' => 'platform_bypass', 'replacement' => '***inappropriate request***', 'severity' => 5],
            ['word' => 'outside platform', 'type' => 'platform_bypass', 'replacement' => '***inappropriate request***', 'severity' => 5],
            ['word' => 'pay directly', 'type' => 'platform_bypass', 'replacement' => '***inappropriate request***', 'severity' => 5],
            ['word' => 'pay you directly', 'type' => 'platform_bypass', 'replacement' => '***inappropriate request***', 'severity' => 5],
            ['word' => 'direct payment', 'type' => 'platform_bypass', 'replacement' => '***inappropriate request***', 'severity' => 5],
            ['word' => 'cheaper price', 'type' => 'platform_bypass', 'replacement' => '***inappropriate request***', 'severity' => 4],
            ['word' => 'lower price', 'type' => 'platform_bypass', 'replacement' => '***inappropriate request***', 'severity' => 4],
            ['word' => 'discount if', 'type' => 'platform_bypass', 'replacement' => '***inappropriate request***', 'severity' => 3],
        ];
        
        foreach ($bypassWords as $word) {
            BannedWord::updateOrCreate(
                ['word' => $word['word'], 'type' => $word['type']],
                [
                    'replacement' => $word['replacement'],
                    'severity' => $word['severity'],
                    'active' => true,
                    'notes' => 'Auto seeded by command'
                ]
            );
        }
        
        $this->info('Platform bypass patterns seeded: ' . count($bypassWords) . ' items');
    }
} 