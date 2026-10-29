<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixBarcodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-barcodes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill missing barcodes for users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = \App\Models\User::whereNull('barcode')->orWhere('barcode', '')->get();
        foreach ($users as $u) {
            do {
                $b = str_pad(mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT);
            } while (\App\Models\User::where('barcode', $b)->exists());
            $u->barcode = $b;
            $u->save();
        }
        $this->info("Barcodes generated for " . $users->count() . " users.");
    }
}
