<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InterestAccrual extends Command
{    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lms:interestaccrual';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interest Accrual';

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
        \App::make('App\Http\Controllers\Lms\DisbursalController')->processAccrualInterest();
    }
}
