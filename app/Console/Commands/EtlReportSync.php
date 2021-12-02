<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EtlReportSync extends Command
{    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etl:ReportSync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ETL Report Sync';

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
        \App::make('App\Http\Controllers\Backend\ReportController')->etlReportSync();
    }
}
