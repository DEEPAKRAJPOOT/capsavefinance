<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\MaturityReport;

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
        // \App::make('App\Http\Controllers\Backend\ReportController')->maturityReport();

        /**
        * @ get all anchors report use $anchor_id = 'all'
        * @ get single anchor report use $anchor_id = 1(anchor_id in numeric)
        */
        MaturityReport::dispatch($needConsolidatedReport = true, $anchor_id = 'all')
                    ->onConnection('database')
                    ->delay(now()->addSeconds(10));
    }
}
