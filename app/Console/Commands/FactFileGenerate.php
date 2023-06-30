<?php

namespace App\Console\Commands;

use Exception;
use Helpers;
use Illuminate\Console\Command;
use Illuminate\Http\Request;


class FactFileGenerate extends Command
{    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fact:FactFileGenerate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fact Excel File Generate';

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
            $tallyJournalData = \DB::table('tally')->select('batch_no')->where(['is_active'=> 1,'is_fact_journal_generated' => 0])->get();
            $tallyPaymentData = \DB::table('tally')->select('batch_no')->where(['is_active'=> 1,'is_fact_payment_generated' => 0])->get();
            if(isset($tallyJournalData)){
                foreach($tallyJournalData as $tallyBatch){
                    $batch_no = $tallyBatch->batch_no ?? NULL;
                    \App::make('App\Http\Controllers\Backend\FinanceController')->processFactJournalTransactions($batch_no);
                }
            }
            if($tallyPaymentData){
                foreach($tallyPaymentData as $tallyBatch){
                    $batch_no = $tallyBatch->batch_no ?? NULL;
                    \App::make('App\Http\Controllers\Backend\FinanceController')->processFactPaymentTransactions($batch_no);
                }
            }
        }catch (Exception $ex) {
            Helpers::getExceptionMessage($ex);
        }
    }
}
