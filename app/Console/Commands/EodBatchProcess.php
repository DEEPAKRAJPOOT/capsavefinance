<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;

class EodBatchProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lms:eodprocess';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'EOD Process';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(InvLmsRepoInterface $lms_repo)
    {
        $this->lmsRepo = $lms_repo;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $status = \App::make('App\Http\Controllers\Lms\EodProcessController')->process();
            if(!$status){
                $eodDetails = $this->lmsRepo->getEodProcess(['is_active'=>1, 'status'=>[config('lms.EOD_PROCESS_STATUS.FAILED'),config('lms.EOD_PROCESS_STATUS.RUNNING')]]);
                if($eodDetails){
                    $eodLog = $this->lmsRepo->getEodProcessLog(['eod_process_id'=>$eodDetails->eod_process_id]);
                    if($eodLog){
                        if($eodLog->tally_status == '2')
                            throw new \Exception('Tally Posting Status Failed!');  
                        if($eodLog->int_accrual_status == '2')
                            throw new \Exception('Interest Accrual Status Failed!');  
                        if($eodLog->repayment_status == '2')
                            throw new \Exception('Repayment Status Failed!');  
                        if($eodLog->disbursal_status == '2')
                            throw new \Exception('Disbursal Status Failed!');  
                        if($eodLog->charge_post_status == '2')
                            throw new \Exception('Charge Posting Status  Failed!');  
                        if($eodLog->overdue_int_accrual_status == '2')
                            throw new \Exception('Overdue Interest Accrual Status  Failed!');  
                        if($eodLog->disbursal_block_status == '2')
                            throw new \Exception('Disbursal Block Status  Failed!');  
                        if($eodLog->running_trans_posting_settled == '2')
                            throw new \Exception('Manually Posted Running Transaction Status  Failed!'); 
                    }else{
                        throw new \Exception("Active EOD log missing");    
                    }
                }else{
                    throw new \Exception("Active EOD record missing");
                }
            }
        } catch (\Exception $e) {
            $this->error("EOD process failed with an exception");
            $this->error($e->getMessage());
            return 2;
        }
    }
}
