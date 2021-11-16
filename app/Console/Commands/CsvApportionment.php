<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Inv\Repositories\Models\Lms\PaymentApportionment;

class CsvApportionment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:day_end_active_csv_apportionment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'End of the day clear all active apportionment csv downloads.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $paymentAppors = PaymentApportionment::where('is_active', 1)
                            //->where('parent_id', 0)
                            ->whereHas('file', function ($query) {
                                $query->whereDate('created_at', now()->format('Y-m-d'));
                            })
                            ->get();
        foreach($paymentAppors as $paymentAppor)
        {
            $paymentAppor->update(['is_active' => 0]);
        }

        $this->info('All today active csv downloads cleared successfully.');
    }
}
