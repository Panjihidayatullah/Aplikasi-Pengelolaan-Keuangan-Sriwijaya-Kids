<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JenisPengeluaran;
use App\Models\Pengeluaran;

class CleanupDuplicateJenisPengeluaran extends Command
{
    protected $signature = 'cleanup:duplicate-jenis-pengeluaran';
    protected $description = 'Remove duplicate jenis pengeluaran and update references';

    public function handle()
    {
        $this->info('Checking for duplicate jenis pengeluaran...');
        
        // Get all names and group
        $allNames = JenisPengeluaran::pluck('nama')->toArray();
        $nameCounts = array_count_values($allNames);
        
        $duplicateCount = 0;
        $removedCount = 0;
        
        foreach($nameCounts as $nama => $count) {
            if($count > 1) {
                $duplicateCount++;
                $this->warn("Found duplicate: {$nama} ({$count}x)");
                
                // Get all items with this name
                $items = JenisPengeluaran::where('nama', $nama)->orderBy('id')->get();
                
                // Keep the first one (lowest ID)
                $keepId = $items->first()->id;
                $this->info("  Keeping ID: {$keepId}");
                
                // Update all pengeluaran that reference the duplicates
                foreach($items->skip(1) as $duplicate) {
                    $affectedRows = Pengeluaran::where('jenis_pengeluaran_id', $duplicate->id)
                        ->update(['jenis_pengeluaran_id' => $keepId]);
                    
                    if($affectedRows > 0) {
                        $this->info("  Updated {$affectedRows} pengeluaran from ID {$duplicate->id} to {$keepId}");
                    }
                    
                    // Delete the duplicate
                    $duplicate->delete();
                    $removedCount++;
                    $this->info("  Deleted duplicate ID: {$duplicate->id}");
                }
                
                $this->line('');
            }
        }
        
        if($duplicateCount == 0) {
            $this->info('No duplicates found!');
        } else {
            $this->info("Summary:");
            $this->info("- Found {$duplicateCount} duplicate categories");
            $this->info("- Removed {$removedCount} duplicate records");
            $this->info("- Total jenis pengeluaran now: " . JenisPengeluaran::count());
        }
        
        return 0;
    }
}
