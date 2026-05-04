<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupLocalJenisPengeluaran extends Command
{
    protected $signature = 'cleanup:local-jenis-pengeluaran {--connection=pgsql}';
    protected $description = 'Remove duplicate jenis pengeluaran from any database connection';

    public function handle()
    {
        $connection = $this->option('connection');
        
        $this->info("Cleaning jenis_pengeluaran on connection: {$connection}");
        $this->newLine();
        
        // Get all names and their counts
        $duplicates = DB::connection($connection)
            ->table('jenis_pengeluaran')
            ->select('nama', DB::raw('count(*) as total'))
            ->groupBy('nama')
            ->having('total', '>', 1)
            ->get();
        
        if ($duplicates->isEmpty()) {
            $this->info('✅ No duplicates found!');
            
            $total = DB::connection($connection)->table('jenis_pengeluaran')->count();
            $this->info("Total jenis pengeluaran: {$total}");
            
            return 0;
        }
        
        $this->warn("Found {$duplicates->count()} duplicate categories");
        $this->newLine();
        
        $removedCount = 0;
        
        foreach ($duplicates as $duplicate) {
            $this->warn("Processing: {$duplicate->nama} ({$duplicate->total}x)");
            
            // Get all items with this name, ordered by ID
            $items = DB::connection($connection)
                ->table('jenis_pengeluaran')
                ->where('nama', $duplicate->nama)
                ->orderBy('id')
                ->get();
            
            // Keep the first one (lowest ID)
            $keepId = $items->first()->id;
            $this->info("  ✓ Keeping ID: {$keepId}");
            
            // Update references in pengeluaran table
            foreach ($items->skip(1) as $item) {
                $affectedRows = DB::connection($connection)
                    ->table('pengeluaran')
                    ->where('jenis_pengeluaran_id', $item->id)
                    ->update(['jenis_pengeluaran_id' => $keepId]);
                
                if ($affectedRows > 0) {
                    $this->info("  ✓ Updated {$affectedRows} pengeluaran records");
                }
                
                // Delete the duplicate
                DB::connection($connection)
                    ->table('jenis_pengeluaran')
                    ->where('id', $item->id)
                    ->delete();
                
                $removedCount++;
                $this->info("  ✓ Deleted duplicate ID: {$item->id}");
            }
            
            $this->newLine();
        }
        
        $this->newLine();
        $this->info("✅ Cleanup completed!");
        $this->info("- Removed {$removedCount} duplicate records");
        
        $total = DB::connection($connection)->table('jenis_pengeluaran')->count();
        $this->info("- Total jenis pengeluaran now: {$total}");
        
        return 0;
    }
}
