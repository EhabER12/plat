<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class CheckStorageLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:check-link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if the storage symbolic link exists and create it if not';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $publicStoragePath = public_path('storage');
        $storageAppPublicPath = storage_path('app/public');
        
        $this->info('Checking storage symbolic link...');
        Log::info('Storage Link Check - Starting check', [
            'public_storage_path' => $publicStoragePath,
            'storage_app_public_path' => $storageAppPublicPath
        ]);
        
        // Check if the public/storage directory exists
        if (file_exists($publicStoragePath)) {
            // Check if it's a symbolic link
            if (is_link($publicStoragePath)) {
                $this->info('Symbolic link exists at: ' . $publicStoragePath);
                Log::info('Storage Link Check - Symbolic link exists', [
                    'target' => readlink($publicStoragePath)
                ]);
                
                // Check if it points to the correct location
                $target = readlink($publicStoragePath);
                if ($target === $storageAppPublicPath) {
                    $this->info('Symbolic link is correctly configured.');
                    Log::info('Storage Link Check - Symbolic link is correctly configured');
                } else {
                    $this->warn('Symbolic link exists but points to: ' . $target);
                    $this->warn('Expected target: ' . $storageAppPublicPath);
                    Log::warning('Storage Link Check - Symbolic link points to incorrect location', [
                        'current_target' => $target,
                        'expected_target' => $storageAppPublicPath
                    ]);
                    
                    // Remove the existing link and create a new one
                    $this->info('Removing incorrect symbolic link...');
                    unlink($publicStoragePath);
                    $this->createSymbolicLink($publicStoragePath, $storageAppPublicPath);
                }
            } else {
                // It exists but it's not a symbolic link
                $this->warn('public/storage exists but is not a symbolic link.');
                Log::warning('Storage Link Check - public/storage exists but is not a symbolic link');
                
                // Backup the existing directory
                $backupPath = public_path('storage_backup_' . time());
                $this->info('Backing up existing directory to: ' . $backupPath);
                File::moveDirectory($publicStoragePath, $backupPath);
                
                // Create the symbolic link
                $this->createSymbolicLink($publicStoragePath, $storageAppPublicPath);
            }
        } else {
            // The public/storage directory doesn't exist, create the symbolic link
            $this->info('public/storage does not exist. Creating symbolic link...');
            Log::info('Storage Link Check - public/storage does not exist');
            $this->createSymbolicLink($publicStoragePath, $storageAppPublicPath);
        }
        
        return 0;
    }
    
    /**
     * Create a symbolic link.
     */
    private function createSymbolicLink($link, $target)
    {
        try {
            // Make sure the target directory exists
            if (!file_exists($target)) {
                $this->info('Creating target directory: ' . $target);
                File::makeDirectory($target, 0755, true);
            }
            
            // Create the symbolic link
            if (symlink($target, $link)) {
                $this->info('Symbolic link created successfully.');
                Log::info('Storage Link Check - Symbolic link created successfully', [
                    'link' => $link,
                    'target' => $target
                ]);
            } else {
                $this->error('Failed to create symbolic link.');
                Log::error('Storage Link Check - Failed to create symbolic link', [
                    'link' => $link,
                    'target' => $target
                ]);
            }
        } catch (\Exception $e) {
            $this->error('Error creating symbolic link: ' . $e->getMessage());
            Log::error('Storage Link Check - Error creating symbolic link', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
