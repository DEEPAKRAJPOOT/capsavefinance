<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Jobs\FactFileTransferJob;
use Carbon\Carbon;

class FactFileTransfer extends Command
{    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fact:FactSftpTransfer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To transfer Fact Report by using sftp';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    // private $request;
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
        try {
            $tallyDatas = \DB::table('tally')->select('id','batch_no','end_date')->where(['is_active'=> 1,'is_fact_journal_generated' => 2, 'is_fact_payment_generated' => 2, 'is_sftp_transfer' => 0])->orderBy('end_date','DESC')->get();
            if($tallyDatas->count() > 0){
                foreach($tallyDatas as $tallyData){
                    $batch_no = $tallyData->batch_no ?? null;
                    $tally_id = $tallyData->id ?? null;
                    $date = Carbon::parse($tallyData->end_date)->format('d-m-Y') ?? null;
                    $journalSourceDir = storage_path('app/public/factDocument/');
                    $journalSourcePath = $journalSourceDir . 'tally_' . $batch_no . '/Fact-Journal-' . $batch_no . '.xlsx';
                    $paymentSourceDir = storage_path('app/public/factDocument/');
                    $paymentSourcePath = $paymentSourceDir . 'tally_' . $batch_no . '/Fact-Payment-' . $batch_no . '.xlsx';
                    if(file_exists($journalSourcePath) && file_exists($paymentSourcePath)){
                        FactFileTransferJob::dispatch($date,$journalSourcePath,$paymentSourcePath,$tally_id);
                    }
                }
            }
            
        }catch (Exception $ex) {
            Helpers::getExceptionMessage($ex);
        }
    }
}
