<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UserInvoiceCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alert:userInvoicePdfMail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'System generated mails for all invoices generated';

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
        \App::make('App\Http\Controllers\Lms\userInvoiceController')->userInvoiceMail();
    //    $lmsRepo = \App::make('App\Inv\Repositories\Contracts\LmsInterface');
        // $getEmail = $lmsRepo->mailsForPendingCases();
         $this->info('alert:userInvoicePdfMail');
    }
}
