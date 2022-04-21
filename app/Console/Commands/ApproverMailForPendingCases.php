<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ApproverMailForPendingCases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alert:approvalMailForPendingCases';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'System generated mails to approvers for pending cases in their tray';

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
       \App::make('App\Http\Controllers\Backend\ApplicationController')->pendingCasesMail();
    //    $lmsRepo = \App::make('App\Inv\Repositories\Contracts\LmsInterface');
        // $getEmail = $lmsRepo->mailsForPendingCases();
         $this->info('alert:approvalMailForPendingCases');
    }
}
