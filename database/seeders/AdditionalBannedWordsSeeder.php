<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BannedWord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdditionalBannedWordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear la tabla si no existe
        if (!Schema::hasTable('banned_words')) {
            Schema::create('banned_words', function ($table) {
                $table->id();
                $table->string('word', 100);
                $table->string('type', 50)->default('general');
                $table->string('replacement', 100)->nullable();
                $table->tinyInteger('severity')->default(1);
                $table->boolean('active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
        
        // Definir palabras y frases prohibidas adicionales
        $bannedWords = [
            // Información de contacto
            ['word' => 'واتساب', 'type' => 'contact_info', 'replacement' => '*****', 'active' => true, 'severity' => 2],
            ['word' => 'رقمي', 'type' => 'contact_info', 'replacement' => '****', 'active' => true, 'severity' => 2],
            ['word' => 'هات رقمك', 'type' => 'contact_info', 'replacement' => '*********', 'active' => true, 'severity' => 2],
            ['word' => 'كلمني واتس', 'type' => 'contact_info', 'replacement' => '**********', 'active' => true, 'severity' => 2],
            
            // Insultos y palabras ofensivas (nivel alto de severidad)
            ['word' => 'ابن الكلب', 'type' => 'profanity', 'replacement' => '**********', 'active' => true, 'severity' => 3],
            ['word' => 'يا ابن الكلب', 'type' => 'profanity', 'replacement' => '************', 'active' => true, 'severity' => 3],
            ['word' => 'ابن الوسخة', 'type' => 'profanity', 'replacement' => '**********', 'active' => true, 'severity' => 3],
            ['word' => 'يا ابن الوسخة', 'type' => 'profanity', 'replacement' => '*************', 'active' => true, 'severity' => 3],
            ['word' => 'ابن العرص', 'type' => 'profanity', 'replacement' => '**********', 'active' => true, 'severity' => 3],
            ['word' => 'يا ابن العرص', 'type' => 'profanity', 'replacement' => '*************', 'active' => true, 'severity' => 3],
            
            // Variaciones con errores ortográficos intencionales
            ['word' => 'ابن ال كلب', 'type' => 'profanity', 'replacement' => '***********', 'active' => true, 'severity' => 3],
            ['word' => 'ابن ال وسخة', 'type' => 'profanity', 'replacement' => '************', 'active' => true, 'severity' => 3],
            ['word' => 'ابن ال عرص', 'type' => 'profanity', 'replacement' => '***********', 'active' => true, 'severity' => 3],
            
            // Palabras individuales ofensivas
            ['word' => 'وسخة', 'type' => 'profanity', 'replacement' => '*****', 'active' => true, 'severity' => 3],
            ['word' => 'عرص', 'type' => 'profanity', 'replacement' => '****', 'active' => true, 'severity' => 3],
            ['word' => 'خول', 'type' => 'profanity', 'replacement' => '****', 'active' => true, 'severity' => 3],
            ['word' => 'متناك', 'type' => 'profanity', 'replacement' => '*****', 'active' => true, 'severity' => 3],
            ['word' => 'شرموط', 'type' => 'profanity', 'replacement' => '*****', 'active' => true, 'severity' => 3],
            ['word' => 'شرموطة', 'type' => 'profanity', 'replacement' => '******', 'active' => true, 'severity' => 3],
            
            // Variaciones adicionales de contacto
            ['word' => 'رقم الواتس', 'type' => 'contact_info', 'replacement' => '**********', 'active' => true, 'severity' => 2],
            ['word' => 'رقم الواتساب', 'type' => 'contact_info', 'replacement' => '************', 'active' => true, 'severity' => 2],
            ['word' => 'رقم تليفوني', 'type' => 'contact_info', 'replacement' => '***********', 'active' => true, 'severity' => 2],
            ['word' => 'رقم موبايلي', 'type' => 'contact_info', 'replacement' => '***********', 'active' => true, 'severity' => 2],
            ['word' => 'تواصل معي', 'type' => 'contact_info', 'replacement' => '**********', 'active' => true, 'severity' => 2],
        ];
        
        // Insertar palabras prohibidas
        foreach ($bannedWords as $word) {
            // Verificar si la palabra ya existe
            $exists = DB::table('banned_words')->where('word', $word['word'])->exists();
            
            if (!$exists) {
                BannedWord::create(array_merge($word, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]));
            }
        }
        
        $this->command->info('Se han agregado ' . count($bannedWords) . ' palabras prohibidas adicionales.');
    }
}
