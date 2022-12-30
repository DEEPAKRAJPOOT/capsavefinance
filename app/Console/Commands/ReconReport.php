<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ReconReport as ReconReportJob;

class ReconReport extends Command
{
    private $emailTo;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'report:reconReport
    {date=now : Date of Recon Report(YYYY/MM/DD)}
    {user=all : The ID of the user}
    {logId=NULL : The ID of the reconReportLog}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Generate Back Dated Recon Report';

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
        $logId = $this->argument('logId');

        if(trim(strtolower($toDate)) == 'now'){
            $toDate = NULL;
        }

        if(trim(strtolower($userId)) == 'all'){
            $userId = NULL;
        }

        if(trim(strtolower($logId)) == 'NULL'){
            $logId = NULL;
        }

        // consolidated report
        ReconReportJob::dispatch($this->emailTo, $userId, $toDate, $logId)
                        ->delay(now()->addSeconds(10));
    }
}
