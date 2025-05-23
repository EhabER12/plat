<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BannedWord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TestBannedWordsSeeder extends Seeder
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
        
        // Limpiar palabras existentes
        DB::table('banned_words')->truncate();
        
        // Definir palabras prohibidas para pruebas
        $bannedWords = [
            // Palabras de prueba (fáciles de probar)
            ['word' => 'test', 'type' => 'general', 'replacement' => '****', 'active' => true, 'severity' => 1],
            ['word' => 'banned', 'type' => 'general', 'replacement' => '******', 'active' => true, 'severity' => 1],
            ['word' => 'محظور', 'type' => 'general', 'replacement' => '******', 'active' => true, 'severity' => 1],
            ['word' => 'كلمة', 'type' => 'general', 'replacement' => '****', 'active' => true, 'severity' => 1],
            
            // Información de contacto
            ['word' => 'whatsapp', 'type' => 'contact_info', 'replacement' => '***', 'active' => true, 'severity' => 2],
            ['word' => 'facebook', 'type' => 'contact_info', 'replacement' => '***', 'active' => true, 'severity' => 2],
            ['word' => 'instagram', 'type' => 'contact_info', 'replacement' => '***', 'active' => true, 'severity' => 2],
            
            // Palabras ofensivas
            ['word' => 'كلب', 'type' => 'profanity', 'replacement' => '****', 'active' => true, 'severity' => 3],
            ['word' => 'حمار', 'type' => 'profanity', 'replacement' => '****', 'active' => true, 'severity' => 3],
            ['word' => 'غبي', 'type' => 'profanity', 'replacement' => '***', 'active' => true, 'severity' => 2],
        ];
        
        // Insertar palabras prohibidas
        foreach ($bannedWords as $word) {
            BannedWord::create(array_merge($word, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
        
        $this->command->info('Se han agregado ' . count($bannedWords) . ' palabras prohibidas para pruebas.');
    }
}
