<?php

namespace App\Console\Commands;

use Helpers;
use Carbon\Carbon;
use App\Helpers\Helper;
use Illuminate\Console\Command;
use App\Helpers\ManualApportionmentHelper;
use App\Inv\Repositories\Contracts\LmsInterface;
use App\Inv\Repositories\Models\Lms\InvoiceDisbursed;
use App\Inv\Repositories\Models\Lms\InvoiceDisbursedDetail;

class InterestAccrualSod extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Lms:interestAccrualSod
    {eventDate? : Event Date(YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate Interest on SOD';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(LmsInterface $lms_repo)
    {
        parent::__construct();
        $this->lmsRepo = $lms_repo;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set("memory_limit", "-1");
        $eventDate = $this->argument('eventDate');
        $eventDate = $eventDate?$eventDate:Carbon::parse(Helpers::getSysStartDate())->format('Y-m-d');
        $cLogDetails = Helper::cronLogBegin(1);
        
            $invoiceList = InvoiceDisbursed::whereNotNull('int_accrual_start_dt')
            ->whereNotNull('payment_due_date')
            ->whereHas('invoice',function($query){ 
                $query->where('is_repayment','0'); 
            })
            ->pluck('invoice_disbursed_id','invoice_disbursed_id');
            $Obj = new ManualApportionmentHelper($this->lmsRepo);
            foreach ($invoiceList as $invId => $trans) {
                $Obj->intAccrual($invId, NULL,NULL,6,$eventDate);
                $Obj->transactionPostingAdjustment($invId,NULL,6,$eventDate);
            }
            $Obj->runningIntPosting(NULL,6,$eventDate);
            InvoiceDisbursedDetail::updateDailyInterestAccruedDetails();

        if($cLogDetails){
            Helper::cronLogEnd('1',$cLogDetails->cron_log_id);
        }
        $this->info('Successfully completed.');
    }
}
