<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ShowTables extends Command
{
    protected $signature = 'db:show-tables';
    protected $description = 'Display all tables in the database';

    public function handle()
    {
        $tables = DB::select('SHOW TABLES');
        
        if (empty($tables)) {
            $this->info('No tables found in the database.');
            return;
        }
        
        $this->info('Tables in the database:');
        foreach ($tables as $table) {
            $tableName = reset($table);
            $this->info("- {$tableName}");
        }
    }
} 