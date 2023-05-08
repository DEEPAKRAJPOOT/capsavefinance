<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MaturityInvoiceDueAlert extends Command
{    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lms:maturityinvoicedueAlert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Maturity Invoice Due Alert';

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
        ini_set("memory_limit", "-1");
        ini_set('max_execution_time', 10000);
        \App::make('App\Http\Controllers\Backend\ReportController')->maturityAlertReport();
    }
}
