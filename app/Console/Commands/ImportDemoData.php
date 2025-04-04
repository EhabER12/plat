<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\DemoDataSeeder;

class ImportDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-demo-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import demo data for courses, categories, and instructors';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('بدء استيراد البيانات الوهمية...');
        
        try {
            $seeder = new DemoDataSeeder();
            $seeder->run();
            
            $this->info('تم استيراد البيانات الوهمية بنجاح!');
        } catch (\Exception $e) {
            $this->error('حدث خطأ أثناء استيراد البيانات: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
        }
        
        return Command::SUCCESS;
    }
} 