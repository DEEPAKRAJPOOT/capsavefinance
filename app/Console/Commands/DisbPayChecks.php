<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Payment;
use Carbon\Carbon;
use App\Helpers\Helper;
use DB;

class DisbPayChecks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'disb_pays:checks 
    {type=0 : The type of report 1 = Duplicate payment Record , 2 = Duplicate Disbursal Report , 3 = Actual Disbursal Report and by default All Reports}
    {report_date=0 : Date of disbursements Report(YYYY/MM/DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To check disbursements and payments of eod ';

    protected $eodDate = '';
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
        if($this->argument('type') == '0'){
            $type = $this->choice(
                'Which type of record you want to execute?',
                ['1','2','3','0'],
                $defaultIndex = '3',
            );
        }else{
            $type = $this->argument('type');
        }
        
        if($this->argument('report_date') == '0'){
            $report_date = $this->choice(
                'Which date of record you want to execute?',
                ['2020-09-08'],
            );
        }else{
            $report_date = $this->argument('report_date');
        }

        $this->eodDate = now()->parse($report_date)->toDateString();
        $reportType = $type;
        ini_set("memory_limit", "-1");
        ini_set('max_execution_time', 10000);

        try {
            $dupPayments=false;
            $dupDisbursals=false;
            $actualDisbursals=false;
            $actualPayment=false;
            $actualRefund=false;
            if($reportType == '1'){
                $dupPayments = $this->checkDuplicatePaymentRecords();
            }else if($reportType == '2'){
                $dupDisbursals = $this->checkDuplicateDisbursalRecords();
            }else if($reportType == '3'){
                $actualDisbursals = $this->checkActualDisbursalAmount();
            }else if($reportType == '4'){
                $actualPayment = $this->checkActualPaymentStatus();
            }else if($reportType == '5'){
                $actualRefund = $this->checkActualRefund();
            }else{
                $dupPayments = $this->checkDuplicatePaymentRecords();
                $dupDisbursals = $this->checkDuplicateDisbursalRecords();
                $actualDisbursals = $this->checkActualDisbursalAmount();
                $actualPayment = $this->checkActualPaymentStatus();
                $actualRefund = $this->checkActualRefund();   
            }
            
            if ($dupPayments || $dupDisbursals || $actualDisbursals || $actualPayment || $actualRefund) {
                $emailData['disbursals'] = $dupDisbursals?$dupDisbursals: [];
                $emailData['payments']   = $dupPayments ? $dupPayments : [];
                $emailData['actualDisbursals'] = $actualDisbursals ? $actualDisbursals : [];
                $emailData['actualPayment'] = $actualPayment ? $actualPayment : [];
                $emailData['actualRefund'] = $actualRefund ? $actualRefund : [];
                \Event::dispatch("NOTIFY_DISB_PAY_CHECKS", serialize($emailData));
            }
        } catch (\Exception $ex) {
            dd(\Helpers::getExceptionMessage($ex));
        }    

        // Disbursal::whereHas('invoice_disbursed', function($query) {
            
        // });
    }

    private function checkDuplicatePaymentRecords()
    {
        DB::enableQueryLog();
        $dupPayments = Payment::withTrashed()->select(DB::raw('GROUP_CONCAT(payment_id) as payment_ids, user_id as customer_id, amount, CONCAT_WS("", utr_no, unr_no, cheque_no) AS com_utr_no, count(*) AS paymentCount'))
                            ->whereDate('created_at', $this->eodDate)
                            ->where('trans_type', config('lms.TRANS_TYPE.REPAYMENT'))
                            ->where('action_type', 1)
                            ->groupBy(['user_id', 'amount', 'utr_no', 'unr_no', 'cheque_no'])
                            ->havingRaw('paymentCount > 1')
                            ->get();
        //dd(DB::getQueryLog());                    
        return $dupPayments;
    }

    private function checkDuplicateDisbursalRecords()
    {
        DB::enableQueryLog();
        $dupDisbursals = Disbursal::select(DB::raw('GROUP_CONCAT(disbursal_id) as disbursal_ids, user_id as customer_id, disburse_amount as amount, count(*) AS disbCount'))
                            ->whereDate('created_at', $this->eodDate)
                            ->groupBy(['user_id', 'disburse_amount', 'disbursal_batch_id'])
                            ->havingRaw('disbCount > 1')
                            ->get();
            //dd(DB::getQueryLog());  
        return $dupDisbursals;
    }

    private function checkActualDisbursalAmount(){
        $prevDate = Carbon::parse($this->eodDate)->subDays(1)->format('Y-m-d');
        $actualDisbursals =  DB::select("SELECT 
                                        a.customer_id,
                                        a.virtual_acc_id,
                                        count(a.disbursal_id) as total_invoice,
                                        SUM(a.invoice_amount) as inv_amount,
                                        GROUP_CONCAT(DISTINCT a.disbursal_id) as disbursal_ids,
                                        GROUP_CONCAT(DISTINCT CONCAT(a.disbursal_id,'|',a.batch_disburse_amount)) as batch_amount,
                                        SUM(a.invoice_approve_amount) as inv_approve_amount,
                                        SUM(a.disbursal_amount) as disbrsl_amnt,
                                        SUM(a.total_interest) as total_interest, 
                                        SUM(a.marginAmt) as marginAmnt,
                                        SUM(a.actual_invoice_disbursed) as actual_invoice_disbursed,
                                        SUM(a.principal_amount) as principle_amount,
                                        SUM(a.trans_amount) as trans_amount,
                                        SUM(ROUND(a.tally_amount,2)) as tally_amount,
                                        a.batch_disburse_amount as batch_disburse_amount,
                                        CASE WHEN SUM(a.principal_amount) = SUM(a.trans_amount) AND SUM(a.principal_amount) = SUM(ROUND(a.tally_amount,2)) AND SUM(a.principal_amount) =  SUM(ROUND(a.disbursal_amount,2)) THEN 'Pass' ELSE 'Fail' END AS result
                                        FROM invdisbtrantallycheck2 AS a 
                                        WHERE a.created_at >= '".$prevDate." 18:30:00' AND a.created_at <= '".$this->eodDate." 18:29:00'
                                        GROUP BY a.customer_id;");
       $actualDisbursals = json_decode(json_encode ( $actualDisbursals ) , true);
       $totalDisbData=[];
       foreach($actualDisbursals as $key=>$value){
           $explodebatch = explode(",",$value['batch_amount']);
           $batchAmount=0.0;
           for($i=0;$i<count($explodebatch);$i++){
            $explodebatchsign = explode("|",$explodebatch[$i]);
            $batchAmount += number_format((float)$explodebatchsign[1], 2, '.', '');
           }
           $value['batch_disburse_amount'] = number_format((float)$batchAmount, 2, '.', '');
           $totalDisbData[] = $value;
       }
       return $totalDisbData;
    }

    public function checkActualPaymentStatus(){
        $prevDate = Carbon::parse($this->eodDate)->subDays(1)->format('Y-m-d');
        $actualPayment =  DB::select("SELECT a.user_id , b.payment_id,b.action_type,b.trans_type,
         b.date_of_payment,SUM(ROUND(a.amount,2)) as transaction_amount,
                                        GROUP_CONCAT(DISTINCT CONCAT(b.payment_id,'|',b.amount,'|',b.is_settled)) as payment_amount, 
                                        SUM(ROUND(c.amount,2)) as tally_amount,
                                        CASE WHEN SUM(ROUND(a.amount,2)) = SUM(ROUND(c.amount,2)) THEN 'Pass' ELSE 'Fail' END AS Result
                                        FROM rta_transactions AS a 
                                        JOIN rta_payments AS b ON a.payment_id = b.payment_id
                                        JOIN rta_tally_entry AS c ON c.transactions_id = a.trans_id
                                        WHERE  a.created_at >= '".$prevDate." 18:30:00' AND a.created_at <= '".$this->eodDate." 18:29:00'
                                        AND a.trans_type NOT IN (2,8,32) AND a.entry_type = 1
                                        GROUP BY a.user_id;");
       $actualPayment = json_decode(json_encode($actualPayment),true); 
       $totalPaymentData=[];
       foreach($actualPayment as $key=>$value){
           $explodePayment = explode(",",$value['payment_amount']);
           $PaymentAmount=0.0;$is_settle=[];
           for($i=0;$i<count($explodePayment);$i++){
            $explodePaymentsign = explode("|",$explodePayment[$i]);
            $PaymentAmount += number_format((float)$explodePaymentsign[1], 2, '.', '');
            $is_settle[] =  $explodePaymentsign[2];
           }
           $value['trans_type'] = array_search($value['trans_type'],config('lms.TRANS_TYPE'),true);
           $value['payment_amount'] = number_format((float)$PaymentAmount, 2, '.', '');
           $value['user_id'] = Helper::formatIdWithPrefix($value['user_id'], 'CUSTID');
           if(in_array(0,$is_settle) || ($value['payment_amount'] != $value['transaction_amount'])){
              $value['Result'] = 'Fail';
           }
           
           $totalPaymentData[] = $value;
       }                    
       return $totalPaymentData;                              
    }

    public function checkActualRefund(){
        $prevDate = Carbon::parse($this->eodDate)->subDays(1)->format('Y-m-d');
        $actualtransRefund = DB::select("SELECT SUM(a.amount) as trans_amount , a.user_id as user_id FROM rta_transactions AS a 
        WHERE a.trans_type = '32'
        AND a.entry_type = '0'
        AND  a.created_at >= '".$prevDate." 18:30:00' AND a.created_at <= '".$this->eodDate." 18:29:00' GROUP by a.user_id");

        $actualRefund = DB::select("SELECT SUM(a.refund_amount) as refund_amount, b.user_id as userId FROM `rta_lms_refund_req` AS a LEFT JOIN rta_payments AS b ON a.payment_id = b.payment_id WHERE a.status = '8' AND a.created_at >= '".$prevDate." 18:30:00' AND a.created_at <= '".$this->eodDate." 18:29:00' GROUP by b.user_id");
        $actualtransRefund = json_decode(json_encode($actualtransRefund),true);
        $actualRefund = json_decode(json_encode($actualRefund),true);
        $totalRefunData =[];
        if(!empty($actualtransRefund) && !empty($actualRefund)){
            foreach($actualtransRefund as $key=>$value){
                $user_id = $value['user_id'];
                $refund_amount = 0.0;
                foreach($actualRefund as $refundValue){
                   $data = array_search($user_id,$refundValue,true);
                   if($data){
                       $refund_amount = $refundValue['refund_amount'];
                   }
                }
                $value['refund_amount'] = $refund_amount;
                if((float)$refund_amount == (float)$value['trans_amount']){
                    $result = 'Pass';
                }else{
                    $result = 'Fail';
                }
                $value['result'] = $result;
                $value['user_id'] = Helper::formatIdWithPrefix($value['user_id'], 'CUSTID');
                $totalRefunData[] = $value;
            }
        }
        return $totalRefunData;

    }

}
