<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\MaturityReport;
use App\Jobs\UtilizationReport;
use App\Jobs\DisbursalReport;

class DailyReport extends Command
{    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lms:dailyReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily Report';

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
        \App::make('App\Http\Controllers\Backend\ReportController')->maturityReport();
    }
}
