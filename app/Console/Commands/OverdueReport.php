<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\OverdueReport as OverdueReportJob;

class OverdueReport extends Command
{
    private $emailTo;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'report:overdue
    {user=all : The ID of the user}
    {date=now : Date of Overdue Report(YYYY/MM/DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Generate Overdue Report';

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
        OverdueReportJob::dispatch($this->emailTo, $userId, $toDate)
                        ->delay(now()->addSeconds(10));
    }
}
