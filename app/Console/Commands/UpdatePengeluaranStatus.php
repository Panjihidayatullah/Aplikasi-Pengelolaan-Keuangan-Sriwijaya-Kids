<?php

namespace App\Console\Commands;

use App\Models\Pengeluaran;
use Illuminate\Console\Command;

class UpdatePengeluaranStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pengeluaran:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status for all pengeluaran records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating pengeluaran status...');

        $pengeluaran = Pengeluaran::all();
        
        // Distribusi: 60% Disetujui, 25% Pending, 15% Ditolak
        $updated = 0;
        foreach ($pengeluaran as $p) {
            $rand = rand(1, 100);
            if ($rand <= 60) {
                $status = 'Disetujui';
            } elseif ($rand <= 85) {
                $status = 'Pending';
            } else {
                $status = 'Ditolak';
            }
            
            $p->status = $status;
            $p->save();
            $updated++;
        }

        $this->info("Updated {$updated} pengeluaran records");
        $this->line('');
        $this->line('Status breakdown:');
        $this->line('Disetujui: ' . Pengeluaran::where('status', 'Disetujui')->count());
        $this->line('Pending: ' . Pengeluaran::where('status', 'Pending')->count());
        $this->line('Ditolak: ' . Pengeluaran::where('status', 'Ditolak')->count());

        return Command::SUCCESS;
    }
}
