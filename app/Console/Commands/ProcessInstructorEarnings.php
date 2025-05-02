<?php

namespace App\Console\Commands;

use App\Models\InstructorEarning;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessInstructorEarnings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'earnings:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending instructor earnings and make them available for withdrawal';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing instructor earnings...');
        
        try {
            // Get the holding period from settings (default: 14 days)
            $holdingPeriod = Setting::where('key', 'earnings_holding_period')
                ->first()->value ?? 14;
            
            // Calculate the date threshold
            $threshold = Carbon::now()->subDays($holdingPeriod);
            
            // Get pending earnings older than the threshold
            $pendingEarnings = InstructorEarning::where('status', InstructorEarning::STATUS_PENDING)
                ->where('created_at', '<', $threshold)
                ->get();
            
            $count = $pendingEarnings->count();
            $this->info("Found {$count} pending earnings to process.");
            
            // Update the status to available
            foreach ($pendingEarnings as $earning) {
                $earning->status = InstructorEarning::STATUS_AVAILABLE;
                $earning->save();
                
                $this->line("Processed earning ID: {$earning->earning_id} for instructor ID: {$earning->instructor_id}");
            }
            
            $this->info("Successfully processed {$count} earnings.");
            Log::info("Processed {$count} instructor earnings from pending to available.");
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('Error processing instructor earnings: ' . $e->getMessage());
            Log::error('Error processing instructor earnings: ' . $e->getMessage());
            
            return Command::FAILURE;
        }
    }
}
