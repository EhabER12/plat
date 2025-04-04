<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportSqlFile extends Command
{
    protected $signature = 'db:import-sql-file {file}';
    protected $description = 'Import SQL file to the database';

    public function handle()
    {
        $file = $this->argument('file');
        $path = base_path($file);
        
        if (!File::exists($path)) {
            $this->error("File not found: {$path}");
            return 1;
        }
        
        $sql = File::get($path);
        
        // Remove -- style comments
        $sql = preg_replace('/--(.*)\n/', '', $sql);
        
        // Split SQL file into individual queries
        $queries = explode(';', $sql);
        
        $this->info("Found " . count($queries) . " queries in file: " . $path);
        $this->info("File size: " . File::size($path) . " bytes");
        
        $count = 0;
        $skipped = 0;
        $failed = 0;
        
        foreach ($queries as $key => $query) {
            $query = trim($query);
            
            if (empty($query)) {
                $skipped++;
                continue;
            }
            
            try {
                // Execute the query
                $this->info("Executing query " . ($key + 1) . ": " . substr($query, 0, 60) . "...");
                DB::statement($query);
                $count++;
                $this->info("Query executed successfully.");
            } catch (\Exception $e) {
                $failed++;
                $this->error("Error executing query: " . substr($query, 0, 100) . "...");
                $this->error("Error message: " . $e->getMessage());
            }
        }
        
        $this->info("Summary: Successfully executed {$count} queries. Skipped: {$skipped}. Failed: {$failed}.");
        
        return ($failed > 0) ? 1 : 0;
    }
} 