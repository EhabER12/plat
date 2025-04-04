<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ShowDatabaseStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:structure {table? : The table to show structure for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the structure of database tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $table = $this->argument('table');
        
        if ($table) {
            $this->showTableStructure($table);
        } else {
            $this->showAllTables();
        }
        
        return Command::SUCCESS;
    }
    
    /**
     * Show the structure of a specific table.
     */
    private function showTableStructure($table)
    {
        $this->info("Structure for table: {$table}");
        
        $columns = DB::select("SHOW COLUMNS FROM {$table}");
        
        $headers = ['Field', 'Type', 'Null', 'Key', 'Default', 'Extra'];
        $rows = [];
        
        foreach ($columns as $column) {
            $rows[] = [
                $column->Field,
                $column->Type,
                $column->Null,
                $column->Key,
                $column->Default,
                $column->Extra,
            ];
        }
        
        $this->table($headers, $rows);
    }
    
    /**
     * Show all tables in the database.
     */
    private function showAllTables()
    {
        $tables = DB::select('SHOW TABLES');
        
        $databaseName = config('database.connections.mysql.database');
        $tableKey = "Tables_in_{$databaseName}";
        
        $tableNames = array_column($tables, $tableKey);
        
        $this->info('Available tables:');
        
        foreach ($tableNames as $tableName) {
            $this->line("- {$tableName}");
        }
        
        $this->info('');
        $this->info('To see the structure of a specific table, use: php artisan db:structure {table}');
    }
}
