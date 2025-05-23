<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\MarkMigrationsAsRun::class,
        Commands\ShowTables::class,
        Commands\CreateMissingTables::class,
        Commands\ImportSqlFile::class,
        Commands\ResetDatabase::class,
        Commands\ProcessInstructorEarnings::class,
        \App\Console\Commands\CleanVideoStorage::class,
        \App\Console\Commands\CreateTestCourseCommand::class,
        \App\Console\Commands\SeedBannedWords::class,
        \App\Console\Commands\CheckStorageLink::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        // Process instructor earnings daily at midnight
        $schedule->command('earnings:process')->dailyAt('00:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}