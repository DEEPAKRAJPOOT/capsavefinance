<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ReceiptReport as ReceiptReportJob;

class ReceiptReport extends Command
{
    private $emailTo;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:receipt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Generate Receipt Report';

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
        ReceiptReportJob::dispatch($this->emailTo)
                        ->delay(now()->addSeconds(10));
    }
}
