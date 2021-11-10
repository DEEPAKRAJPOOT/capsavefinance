<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\MarginReport as MarginReportJob;

class MarginReport extends Command
{
    private $emailTo;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:margin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Generate Margin Report';

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

        // consolidated report
        MarginReportJob::dispatch($this->emailTo)
                        ->delay(now()->addSeconds(10));
    }
}
