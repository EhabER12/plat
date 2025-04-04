<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetDatabase extends Command
{
    protected $signature = 'db:reset';
    protected $description = 'Reset the database by dropping and recreating it';

    public function handle()
    {
        $dbName = config('database.connections.mysql.database');
        
        if (empty($dbName)) {
            $this->error('Database name not found in configuration!');
            return 1;
        }
        
        $this->info("Resetting database: {$dbName}");
        
        try {
            // Try to drop the database if it exists
            DB::statement("DROP DATABASE IF EXISTS {$dbName}");
            $this->info("Database {$dbName} dropped (if it existed).");
            
            // Create the database
            DB::statement("CREATE DATABASE {$dbName}");
            $this->info("Database {$dbName} created.");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Error resetting database: " . $e->getMessage());
            return 1;
        }
    }
} 