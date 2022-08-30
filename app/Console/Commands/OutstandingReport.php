<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\OutstandingReportManual as OutstandingReportManualJob;

class OutstandingReport extends Command
{
    private $emailTo;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'report:outstandingManual
    {date=now : Date of Outstanding Report(YYYY/MM/DD)}
    {user=all : The ID of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Generate Back Dated Outstanding Report';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->emailTo = config('lms.DAILY_REPORT_MAIL');
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
        if (empty($this->emailTo)) {
            dd('DAILY_REPORT_MAIL is missing');
        }

        $userId = $this->argument('user');
        $toDate = $this->argument('date');

        if(trim(strtolower($toDate)) == 'now'){
            $toDate = NULL;
        }

        if(trim(strtolower($userId)) == 'all'){
            $userId = NULL;
        }

        // consolidated report
        OutstandingReportManualJob::dispatch($this->emailTo, $userId, $toDate)
                        ->delay(now()->addSeconds(10));
    }
}
