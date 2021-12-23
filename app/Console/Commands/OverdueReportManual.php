<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\OverdueReportManual as OverdueReportManualJob;

class OverdueReportManual extends Command
{
    private $emailTo;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'report:overdueManual
    {user=all : The ID of the user}
    {date=now : Date of Overdue Report(YYYY/MM/DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Generate Back Dated Overdue Report';

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
        OverdueReportManualJob::dispatch($this->emailTo, $userId, $toDate)
                        ->delay(now()->addSeconds(10));
    }
}
