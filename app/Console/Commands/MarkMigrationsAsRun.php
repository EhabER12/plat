<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MarkMigrationsAsRun extends Command
{
    protected $signature = 'migrations:mark-as-run';
    protected $description = 'Mark all pending migrations as run without actually running them';

    public function handle()
    {
        // Get all migration files from the migrations directory
        $migrationFiles = File::glob(database_path('migrations/*.php'));
        
        // Get the current batch number
        $batch = DB::table('migrations')->max('batch') + 1;
        
        // Get already migrated files
        $migratedFiles = DB::table('migrations')->pluck('migration')->toArray();
        
        $count = 0;
        
        foreach ($migrationFiles as $file) {
            // Extract the migration name without extension
            $migrationName = pathinfo($file, PATHINFO_FILENAME);
            
            // Skip if already migrated
            if (in_array($migrationName, $migratedFiles)) {
                $this->info("Migration {$migrationName} already marked as run. Skipping.");
                continue;
            }
            
            // Add to migrations table
            DB::table('migrations')->insert([
                'migration' => $migrationName,
                'batch' => $batch
            ]);
            
            $this->info("Marked migration {$migrationName} as run.");
            $count++;
        }
        
        $this->info("Marked {$count} migrations as run.");
    }
} 