<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarkMigrationAsRunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the latest batch number
        $latestBatch = DB::table('migrations')->max('batch');
        
        // Insert the migration record
        DB::table('migrations')->insert([
            'migration' => '2025_04_20_000002_create_instructor_earnings_table',
            'batch' => $latestBatch + 1,
        ]);
        
        $this->command->info('Migration 2025_04_20_000002_create_instructor_earnings_table marked as run.');
    }
}
