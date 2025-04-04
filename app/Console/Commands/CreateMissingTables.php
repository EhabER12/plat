<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateMissingTables extends Command
{
    protected $signature = 'db:create-missing-tables';
    protected $description = 'Create all missing tables from migration files';

    private $existingTables = [];

    public function handle()
    {
        // Get existing tables
        $this->existingTables = $this->getExistingTables();
        $this->info("Found " . count($this->existingTables) . " existing tables in the database.");
        
        // Get all migration files
        $migrationFiles = File::glob(database_path('migrations/*.php'));
        $this->info("Found " . count($migrationFiles) . " migration files.");
        
        $count = 0;
        $skipped = 0;
        $failed = 0;
        
        foreach ($migrationFiles as $file) {
            $migrationName = pathinfo($file, PATHINFO_FILENAME);
            
            // Check if this migration creates a table
            if (strpos($migrationName, 'create_') === false) {
                $this->info("Skipping non-table migration: {$migrationName}");
                $skipped++;
                continue;
            }
            
            // Extract table name from migration name (e.g., 'create_users_table' => 'users')
            $tableName = $this->extractTableName($migrationName);
            
            if (!$tableName) {
                $this->info("Skipping migration with unrecognized format: {$migrationName}");
                $skipped++;
                continue;
            }
            
            // Skip if table already exists
            if (in_array($tableName, $this->existingTables)) {
                $this->info("Table '{$tableName}' already exists. Skipping.");
                $skipped++;
                continue;
            }
            
            // Try to run the migration
            $this->info("Creating table '{$tableName}' from migration: {$migrationName}");
            
            try {
                // Run the migration file directly
                require_once $file;
                
                // Laravel 8+ uses anonymous classes in migrations, so we need to call the factory function
                $migrationInstance = require $file;
                
                // Call the up method
                DB::beginTransaction();
                $migrationInstance->up();
                DB::commit();
                
                $this->existingTables[] = $tableName; // Add to our list of existing tables
                $this->info("Successfully created table '{$tableName}'.");
                $count++;
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Failed to create table '{$tableName}': " . $e->getMessage());
                $failed++;
            }
        }
        
        $this->info("Summary: Created {$count} tables, skipped {$skipped} migrations, failed {$failed} migrations.");
    }
    
    private function getExistingTables()
    {
        $tables = [];
        $results = DB::select('SHOW TABLES');
        
        foreach ($results as $result) {
            $tables[] = reset($result);
        }
        
        return $tables;
    }
    
    private function extractTableName($migrationName)
    {
        // Match patterns like create_users_table or create_blog_posts_table
        if (preg_match('/create_(.+)_table/', $migrationName, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
} 