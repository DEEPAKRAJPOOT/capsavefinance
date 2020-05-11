<?php
namespace App\Inv\Repositories\Models\Lms;

use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class Transactions extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    private static $balacnce = 0;
    protected $table = 'transactions';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'trans_id';

    /**
     * Maintain created_at and updated_at automatically
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Maintain created_by and updated_by automatically
     *
     * @var boolean
     */
    public $userstamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_id',
        'link_trans_id',
        'parent_trans_id',
        'invoice_disbursed_id',
        'trans_running_id',
        'user_id',
        'trans_date',
        'trans_type',
        'amount',
        'entry_type',
        'gst',
        'tds_per',
        'gl_flag',
        'soa_flag',
        'trans_by',
        'pay_from',
        'is_settled',
        'is_posted_in_tally',
        'created_at',
        'created_by'
    ];

    public function payment(){
        return $this->belongsTo('App\Inv\Repositories\Models\Payment','payment_id','payment_id');
    } 

    public function invoiceDisbursed(){
        return $this->belongsTo('App\Inv\Repositories\Models\Lms\InvoiceDisbursed','invoice_disbursed_id','invoice_disbursed_id');
    }

    public function biz() {
       return $this->belongsTo('App\Inv\Repositories\Models\Business', 'biz_id');
    }

    public function disburse() {
       return $this->belongsTo('App\Inv\Repositories\Models\Lms\InvoiceDisbursed', 'invoice_disbursed_id');
    }
        
    public function user(){
        return $this->belongsTo('App\Inv\Repositories\Models\User','user_id','user_id');
    }
    
    public function lmsUser(){
        return $this->belongsTo('App\Inv\Repositories\Models\LmsUser','user_id','user_id');
    }

    public function transType(){
       return $this->belongsTo('App\Inv\Repositories\Models\Lms\TransType', 'trans_type', 'id');
    }   
  
    public function refundTransaction(){
        return $this->hasOne('App\Inv\Repositories\Models\Lms\RefundTransactions', 'new_trans_id', 'trans_id');
    }

    public function accruedInterest(){
        return $this->hasMany('App\Inv\Repositories\Models\Lms\InterestAccrual','invoice_disbursed_id','invoice_disbursed_id');
    }

    public function userInvTrans(){
        return $this->hasOne('App\Inv\Repositories\Models\Lms\UserInvoiceTrans','trans_id','trans_id');
    } 

    public function userInvParentTrans(){
        return $this->hasOne('App\Inv\Repositories\Models\Lms\UserInvoiceTrans','trans_id','parent_trans_id');
    }

    public function refundReqTrans(){
        return $this->hasMany('App\Inv\Repositories\Models\Lms\Refund\RefundReqTrans','trans_id','trans_id');
    }

    public function transRunning(){
        return $this->belongsTo('App\Inv\Repositories\Models\Lms\TransactionsRunning','trans_running_id','trans_running_id');
    }

    public function getInvoiceNoAttribute(){
        $data = '';
        if($this->userInvTrans){
            return $this->userInvTrans->getUserInvoice->invoice_no;
        }elseif($this->userInvParentTrans){
            return $this->userInvParentTrans->getUserInvoice->invoice_no;
        }elseif($this->invoice_disbursed_id && $this->invoiceDisbursed->invoice_id){
            return $this->invoiceDisbursed->invoice->invoice_no;
        }
        return $data;
    }

    public function getsettledAmtAttribute(){
       
        $dr = self::where('parent_trans_id','=',$this->trans_id)
        ->where('entry_type','=','0')
        ->whereNotIn('trans_type',[config('lms.TRANS_TYPE.REFUND')])
        ->sum('amount');

        $cr = self::where('parent_trans_id','=',$this->trans_id)
        ->where('entry_type','=','1')        
        ->sum('amount');

        return (float)$cr - (float)$dr;
    }

    public function getOutstandingAttribute(){
        return round(($this->amount - $this->getsettledAmtAttribute()),2);
    }

    public function getDpdAttribute(){
        $to = \Carbon\Carbon::now()->setTimezone(config('common.timezone'));
        
        if($this->trans_type == config('lms.TRANS_TYPE.PAYMENT_DISBURSED')){
            $from = date('Y-m-d',strtotime($this->invoiceDisbursed->payment_due_date));
        }
        elseif($this->trans_type == config('lms.TRANS_TYPE.INTEREST')){
            if($this->invoiceDisbursed->invoice->program_offer->payment_frequency <> 1){
                $from = date('Y-m-d',strtotime($this->trans_date));
            }else{
                $from = date('Y-m-d',strtotime($this->invoiceDisbursed->payment_due_date));
            }
        }

        return $to->diffInDays($from);
    }

    public function getWaiveOffAmount(){
        return self::where(['parent_trans_id' => $this->trans_id, 'trans_type' => 36])->sum('amount');
    }

    public function getRefundableAmtAttribute(){
        return self::where('link_trans_id','=',$this->trans_id)
        ->where('entry_type','=','0')
        ->whereIn('trans_type',[config('lms.TRANS_TYPE.REVERSE'),config('lms.TRANS_TYPE.REFUND')]) 
        ->sum('amount');
    }

    public function getRefundOutstandingAttribute(){
        return round(($this->amount - $this->getRefundableAmtAttribute()),2);
    }

    public function getParentTransDateAttribute(){
        $transDate = '';
        $parentTrans = self::find($this->parent_trans_id);
        if($parentTrans){
            $transDate = $parentTrans->trans_date;
        }else{
            $transDate = $this->trans_date;
        }
        return $transDate;
    }

    public function getTransNameAttribute(){
        $name = ' '; 
       
        if(in_array($this->trans_type,[config('lms.TRANS_TYPE.WAVED_OFF'),config('lms.TRANS_TYPE.TDS'),config('lms.TRANS_TYPE.REVERSE'),config('lms.TRANS_TYPE.REFUND'),config('lms.TRANS_TYPE.CANCEL')])){
            if($this->parent_trans_id){
                $parentTrans = self::find($this->parent_trans_id);
                $name .= $parentTrans->transType->trans_name.' ';
                if($this->link_trans_id){
                    $linkTrans = self::find($this->link_trans_id);
                    if(in_array($linkTrans->trans_type,[config('lms.TRANS_TYPE.WAVED_OFF'),config('lms.TRANS_TYPE.TDS'),config('lms.TRANS_TYPE.REVERSE'),config('lms.TRANS_TYPE.CANCEL')]))
                        $name .= $linkTrans->transType->trans_name.' ';
                }
            }
        }

        if($this->entry_type == 0){
            $name .= $this->transType->debit_desc;
        }elseif($this->entry_type == 1){
            $name .= $this->transType->credit_desc;
        }
        return $name;
    }

    public function getBatchNoAttribute(){

        if($this->entry_type == 0 && $this->invoice_disbursed_id && !in_array($this->trans_type,[
            config('lms.TRANS_TYPE.REVERSE'),
            config('lms.TRANS_TYPE.TDS'),
            config('lms.TRANS_TYPE.WAVED_OFF'),
            config('lms.TRANS_TYPE.REFUND')
        ])){
            return $this->invoiceDisbursed->disbursal->disbursal_batch->batch_id;
        }
    }

    public function getNarrationAttribute(){
        $data = '';
        if(in_array($this->trans_type,[ config('lms.TRANS_TYPE.REPAYMENT')])){
            $data .= $this->BatchNo.' ';
            $data .= $this->payment->paymentmode.': '.$this->payment->transactionno.' ';   
            $data .= ' Payment Allocated as Normal: INR '. number_format($this->payment->amount,2) . ' '; 
        }
        return $data;
    }

    public static function get_balance($trans_code,$user_id){

        $dr = self::whereRaw('concat_ws("",user_id, DATE_FORMAT(created_at, "%y%m%d"), (1000000000+trans_id)) <= ?',[$trans_code])
            ->where('user_id','=',$user_id)
            ->where('entry_type','=','0')
            ->where('soa_flag','=',1)
            ->sum('amount');
                                        
        $cr = self::whereRaw('concat_ws("",user_id, DATE_FORMAT(created_at, "%y%m%d"), (1000000000+trans_id)) <= ?',[$trans_code])
            ->where('user_id','=',$user_id)
            ->where('entry_type','=','1')
            ->where('soa_flag','=',1)
            ->sum('amount');

        return $dr - $cr;
    }
    
    public  function getBalanceAttribute()
    {
        return self::get_balance($this->user_id.Carbon::parse($this->created_at)->format('ymd').(1000000000+$this->trans_id), $this->user_id);
    }

    public static function getUnsettledTrans($userId){
        return self::whereIn('is_settled',[0,1])
                ->whereNull('parent_trans_id')
                ->where('user_id','=',$userId)
                ->orderBy('invoice_disbursed_id','ASC')
                ->with(array('invoiceDisbursed' => function($query) {
                    $query->orderBy('int_accrual_start_dt','ASC');
                }))
                ->orderBy('trans_date','ASC')
                ->orderByRaw("FIELD(trans_type, '".config('lms.TRANS_TYPE.INTEREST')."', '".config('lms.TRANS_TYPE.PAYMENT_DISBURSED')."', '".config('lms.TRANS_TYPE.INTEREST_OVERDUE')."', '".config('lms.TRANS_TYPE.MARGIN')."' )")
                ->get()
                ->filter(function($item) {
                    return $item->outstanding > 0;
                });
    }

    public static function getSettledTrans($userId){
        return self::where('entry_type','1')
                //->whereNotNull('parent_trans_id')
                ->whereNotIn('trans_type',[config('lms.TRANS_TYPE.REFUND'),config('lms.TRANS_TYPE.REVERSE')])
                ->where('user_id','=',$userId)->get()
                ->filter(function($item){
                    return $item->refundoutstanding > 0;
                });
    }

    public static function getRefundTrans($userId){
        return self::where('entry_type','1')
                //->whereNotNull('parent_trans_id')
                ->whereIn('trans_type',[config('lms.TRANS_TYPE.REFUND'),config('lms.TRANS_TYPE.TDS'),config('lms.TRANS_TYPE.MARGIN'),config('lms.TRANS_TYPE.NON_FACTORED_AMT')])
                ->where('user_id','=',$userId)->get()
                ->filter(function($item){
                   return $item->refundoutstanding > 0;
                });
    }

    /**
     * Save Transactions
     * 
     * @param array $transactions
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function saveTransaction($transactions,$whereCondition=[])
    {
        if (!is_array($transactions)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        if (!isset($transactions['created_at'])) {
            $transactions['created_at'] = \Carbon\Carbon::now()->setTimezone(config('common.timezone'))->format('Y-m-d h:i:s');
        }
        if (!isset($transactions['created_by'])) {
            $transactions['created_by'] = \Auth::user()->user_id;
        }        
        
        if (!empty($whereCondition)) {
            return self::where($whereCondition)->update($transactions);
        } else if (!isset($transactions[0])) {
            return self::create($transactions);
        } else {            
            return self::insert($transactions);
        }
    }

    /**
     * Get Transactions
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getTransactions($whereCondition=[])
    {
        if (!is_array($whereCondition)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        $query = self::select('*');
                
        if (!empty($whereCondition)) {
            $query->where($whereCondition);
        }
        
        $result = $query->get();
        return $result;
    }

    public static function getUnsettledInvoices($data = [])
    {
        $query = self::whereIn('trans_type',[config('lms.TRANS_TYPE.PAYMENT_DISBURSED'),config('lms.TRANS_TYPE.INTEREST')])
        ->whereNull('payment_id')  
        ->whereNull('link_trans_id')  
        ->whereNull('parent_trans_id');
        
        if(isset($data['int_accrual_start_dt'])){
            $query->whereHas('invoiceDisbursed', function($q) use($data){
                $q->where('int_accrual_start_dt','<=',$data['int_accrual_start_dt']);
                $q->orderBy('inv_due_date','ASC');
                $q->orderBy('disbursal_id','ASC');
            });
        }

        if(isset($data['user_id'])){
            $query->where('user_id','=',$data['user_id']);
        }

        $transactions = $query->get();

        $unsettledInvoices = [];
        foreach ($transactions as $key => $value) {
            if(isset($unsettledInvoices[$value->invoice_disbursed_id]) && $value->outstanding > 0){
                $unsettledInvoices[$value->invoice_disbursed_id] += $value->outstanding;
            }elseif($value->outstanding > 0){
                $unsettledInvoices[$value->invoice_disbursed_id] = $value->outstanding;
            }
        }

        return $unsettledInvoices;
    }

    public static function getUnsettledInvoiceTransactions($data = [])
    {
       
        $query =  self::whereNull('parent_trans_id')->whereNull('payment_id');

        if(isset($data['invoice_disbursed_id'])){
            $query->where('invoice_disbursed_id',$data['invoice_disbursed_id']);
        }

        if(isset($data['user_id'])){
            $query->where('user_id',$data['user_id']);
        }

        if(isset($data['trans_type']) && !empty($data['trans_type'])){
            $query->whereIn('trans_type',$data['trans_type']);
        }

        $query->orderByRaw("FIELD(trans_type, '".config('lms.TRANS_TYPE.INTEREST')."', '".config('lms.TRANS_TYPE.PAYMENT_DISBURSED')."', '".config('lms.TRANS_TYPE.INTEREST_OVERDUE')."', '".config('lms.TRANS_TYPE.MARGIN')."' )");
        $query->orderby('trans_date','asc');
        return $query->get()->filter(function($item) {
            return $item->outstanding > 0;
        });
    }

    public static function getUnsettledChargeTransactions($data = [])
    {
        $query =  self::whereNull('parent_trans_id')->whereNull('payment_id');

        if(isset($data['user_id'])){
            $query->where('user_id',$data['user_id']);
        }

        if(isset($data['trans_type']) && !empty($data['trans_type'])){
            $query->whereIn('trans_type',$data['trans_type']);
        }

        if(!empty($data['trans_type_not_in'])){
            $query->whereNotIn('trans_type',$data['trans_type_not_in']);
        }
        $query->orderBy('trans_date','ASC');

        return $query->get()->filter(function($item) {
            return $item->outstanding > 0;
        });
    }

    public static function calInvoiceRefund($invDesbId,$payment_date=null)
    {
        $intRefund = 0;
        $invoice2 = null;

        $invoice = self::where('invoice_disbursed_id','=',$invDesbId)
        ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
        ->where('entry_type', '0')
        ->whereHas('invoiceDisbursed',function($query){
            $query->whereHas('invoice', function($query){
                $query->whereHas('program_offer',function($query){
                    $query->where('payment_frequency','=',1);
                });
            });
        })
        ->first();
        $invoice2 = $invoice;
        if($invoice){
            
            $totalDebitAmt = self::where('entry_type','=','0')
            ->where('invoice_disbursed_id','=',$invDesbId)
            ->whereIn('trans_type',[config('lms.TRANS_TYPE.PAYMENT_DISBURSED')])
            ->sum('amount');
        
            $totalCreditAmt =  self::where('entry_type','=','1')
            ->where('invoice_disbursed_id','=',$invDesbId)
            ->whereIn('trans_type',[config('lms.TRANS_TYPE.PAYMENT_DISBURSED')])
            ->sum('amount');

            if($totalDebitAmt <= $totalCreditAmt){
                $accruedInterest = $invoice->accruedInterest();
                if($payment_date){
                    $accruedInterest = $accruedInterest->whereDate('interest_date','<',$payment_date);
                } 
                $totalInterestAmt = round($accruedInterest->sum('accrued_interest'),2);

                $credit = [];
                $debit = [];
                $interestTrails = self::where('parent_trans_id','=',$invoice->trans_id)->get()->toArray();
                foreach ($interestTrails as $key => $value) {
                    if($value['entry_type'] == '1'){
                        if(isset($data[$value['trans_type']])){
                            $credit[$value['trans_type']] += $value['amount'];
                        }else{
                            $credit[$value['trans_type']] = $value['amount'];
                        }
                    }
                    else{
                        if(isset($debit[$value['trans_type']])){
                            $debit[$value['trans_type']] += $value['amount'];
                        }else{
                            $debit[$value['trans_type']] = $value['amount'];
                        }
                    }
                }

                $interestDue = (float)$invoice->amount;
                $interestSettled = isset($credit[config('lms.TRANS_TYPE.INTEREST')])?(float)$credit[config('lms.TRANS_TYPE.INTEREST')]:0;
                $interestToBeRefunded = isset($credit[config('lms.TRANS_TYPE.REFUND')])?(float)$credit[config('lms.TRANS_TYPE.REFUND')]:0;
                $interestWaivedOff = isset($credit[config('lms.TRANS_TYPE.WAVED_OFF')])?(float)$credit[config('lms.TRANS_TYPE.WAVED_OFF')]:0;
                $interestTDS = isset($credit[config('lms.TRANS_TYPE.TDS')])?(float)$credit[config('lms.TRANS_TYPE.TDS')]:0;
                $interestRefunded = isset($debit[config('lms.TRANS_TYPE.REFUND')])?(float)$debit[config('lms.TRANS_TYPE.REFUND')]:0;
                $interestReversed = isset($debit[config('lms.TRANS_TYPE.REVERSE')])?(float)$debit[config('lms.TRANS_TYPE.REVERSE')]:0;

                $interestRemainingRefund = ($interestDue+$interestReversed)-($interestWaivedOff)-($interestTDS)-($interestToBeRefunded);

                $intRefund = round(($interestRemainingRefund)-($totalInterestAmt),2);
                
                $intRefund = ($intRefund <= 0)?0:$intRefund;
            }
        }

        return collect(['amount'=> $intRefund,'parent_transaction'=>$invoice2]);
    }

    public static function getJournals(array $whereCond = []){
        return self::whereNull('payment_id')
            ->where($whereCond)
            ->get();
    }

    public static function getMaxDpdTransaction($userId, $transType){
        $transactions =  self::whereNull('parent_trans_id')
        ->whereNull('payment_id')
        ->where('user_id','=',$userId)
        ->where('trans_type','=',$transType)        
        ->orderByRaw("FIELD(trans_type, '".config('lms.TRANS_TYPE.INTEREST')."', '".config('lms.TRANS_TYPE.PAYMENT_DISBURSED')."', '".config('lms.TRANS_TYPE.INTEREST_OVERDUE')."', '".config('lms.TRANS_TYPE.MARGIN')."' )")
        ->get()
        ->filter(function($item) {
            return $item->outstanding > 0;
        });
        $maxDPD = $transactions->max('dpd');
        return $transactions->where('dpd','=',$maxDPD)->first();
    }

    /*** save repayment transaction details for invoice  **/
    public static function saveRepaymentTrans($attr)
    {
          return self::create($attr);
    }
    
    /*** save repayment transaction details for invoice  **/
    public static function saveCharge($attr)
    {
        return self::create($attr);
    } 
    
    /*** get all transaction  **/
    public static function getAllManualTransaction()
    {
          return self::with(['biz','disburse','user', 'transType'])->where('trans_by','!=',NULL)->orderBy('trans_id','DESC');
    }  

    /*** get transaction  Detail**/
    public static function getTransDetail($whereCondition){
          return self::with(['biz','disburse','user', 'transType'])->where($whereCondition)->first();
    }

    public static function getUserBalance($user_id){
        $dr = self::where('user_id','=',$user_id)
        ->where('entry_type','=','0')
        ->sum('amount');

        $cr = self::where('user_id','=',$user_id)
        ->where('entry_type','=','1')        
        ->sum('amount');

        return $dr-$cr;

    }

     /*** get all transaction  **/
    public static function getAllUserChargeTransaction() {
          return self::with('user')->groupBy('user_id')->get();
    }   
     
    /**
     * Update Transaction
     * 
     * @param array $whereCondition
     * @param array $data
     * @return mixed
     */
    public static function updateTransaction($whereCondition, $data) {
        return self::where($whereCondition)->update($data);
    }

    public static function updateIsInvoiceGenerated($transIds, $data) {
        return self::whereIn('trans_id', $transIds)->update($data);
    }

    public function getOppTransNameAttribute(){
        if($this->transType->chrg_master_id!='0'){
            if($this->is_waveoff == 1){
                return $this->transType->charge->chrg_name.' Waved Off';
            }if($this->is_tds == 1){
                return $this->transType->charge->chrg_name.' TDS';
            }elseif($this->entry_type == 0){
                return $this->transType->charge->credit_desc;
            }elseif($this->entry_type == 1){
                return $this->transType->charge->debit_desc;
            }
        }else{
            if($this->is_waveoff == 1){
                return $this->transType->trans_name.' Waved Off';
            }if($this->is_tds == 1){
                return $this->transType->trans_name.' TDS';
            }elseif($this->entry_type == 0){
                return $this->transType->credit_desc;
            }elseif($this->entry_type == 1){
                return $this->transType->debit_desc;
            }
        }
    }

    public function getModeOfPaymentNameAttribute(){
        $modeName = '';
        switch ($this->mode_of_pay) {
            case '1':
                $modeName = 'UTR No';
                break;
            case '2':
                $modeName = 'Cheque No';
                break;
            case '3':
                $modeName = 'URN No';
                break;  
        }
        return $modeName;
    }

    public function getModeOfPaymentNoAttribute(){
        $modeNo = '';
        switch ($this->mode_of_pay) {
            case '1':
                $modeNo = $this->utr_no;
                break;
            case '2':
                $modeNo = $this->cheque_no;
                break;
            case '3':
                $modeNo = $this->unr_no;
                break;   
        }
        return $modeNo;
    }



    public static function getSoaList(){

        return self::select('transactions.*')
                    ->join('users', 'transactions.user_id', '=', 'users.user_id')
                    ->join('lms_users','users.user_id','lms_users.user_id')
                    ->where('soa_flag','=',1)
                    //->where('amount','>',0)
                    ->orderBy('user_id', 'asc')
                    ->orderBy(DB::raw("DATE_FORMAT(rta_transactions.created_at, '%Y-%m-%d')"), 'asc')
                    ->orderBy('trans_id', 'asc');
    }
    
    /**
     * Get Repayment Amount
     * 
     * @param integer $userId
     * @param integer $transType
     * @return mixed
     */
    public static function getRepaymentAmount($userId, $transType)
    {
        //Calculate Debit Amount
        $result = self::select(DB::raw('SUM(amount) AS amount, gst, SUM(cgst) AS cgst, SUM(sgst) AS sgst, SUM(igst) AS igst'))
                ->whereIn('is_settled', [0,1])
                ->where('user_id', $userId)
                ->where('entry_type', '0')    //Debit
                ->where('trans_type', $transType)
                ->groupBy('user_id')
                ->first();
        $debitAmtData = $result ? $result->toArray() : [];
        
        //Calculate Credit Amount
        $result = self::select(DB::raw('SUM(amount) AS amount, gst, SUM(cgst) AS cgst, SUM(sgst) AS sgst, SUM(igst) AS igst'))
                ->whereIn('is_settled', [0,1])
                ->where('user_id', $userId)
                ->where('entry_type', '1')
                ->where('trans_type', $transType)
                ->groupBy('user_id')
                ->first();
        $creditAmtData = $result ? $result->toArray() : [];
        
        $repaymentAmount = ['debitAmtData' => $debitAmtData, 'creditAmtData' => $creditAmtData];
        
        return $repaymentAmount;
    }

    public function userinvoicetrans(){
       return $this->hasOne('App\Inv\Repositories\Models\Lms\UserInvoiceTrans', 'trans_id', 'trans_id');
    }

    
    public static function getAllChargesApplied(array $where = array()) {
        $cond = '';
        if (!empty($where)) {
            foreach ($where as $key => $value) {
                $wh[] = "t1.$key = '$value'";
            }
           $cond = ' AND ' .implode(' AND ', $wh);
        }
        $query = "SELECT DATE_FORMAT(t1.trans_date, '%d/%m/%Y') as trans_date, t1.trans_id, t1.parent_trans_id, t1.trans_name, t1.trans_desc, t1.user_id, t1.entry_type, t1.amount AS debit_amount, IFNULL(SUM(t2.amount), 0) as credit_amount, (t1.amount - IFNULL(SUM(t2.amount), 0)) as remaining 
        FROM `get_all_charges` t1 
        LEFT JOIN rta_transactions as t2 ON t1.trans_id = t2.parent_trans_id 
        WHERE t1.entry_type = 0  ". $cond ." GROUP BY t1.trans_id";
        $result = \DB::SELECT(\DB::raw($query));
        return $result;
    }
    
    public static function getTallyTxns(array $where = array()) {
        return self::with('payment', 'user', 'invoiceDisbursed', 'lmsUser', 'transType', 'userinvoicetrans')->where($where)->get();
    }

    
    public function refundReq(){
        return $this->hasOne('App\Inv\Repositories\Models\Lms\Refund\RefundReq','payment_id','payment_id');
    }

    public function getParentTxn() {
        return self::with('payment', 'user', 'invoiceDisbursed', 'lmsUser', 'transType', 'userinvoicetrans')->where('trans_id', $this->parent_trans_id)->first();
    }

    public function userRelation() {
       return $this->hasOne('App\Inv\Repositories\Models\Lms\UserInvoiceRelation', 'user_id', 'user_id')->where('is_active', 1);
    }

    public static function getUserInvoiceTxns($userId, $invoiceType, $trans_ids, $is_force = false){
       $sql = self::with('transType')->whereNull('payment_id')->where(['user_id' => $userId, 'entry_type' => 0]);
       if (!empty($trans_ids)) {
        if ($is_force) {
            $sql->where('is_invoice_generated', '=', 0);
        }
        $sql->whereIn('trans_id', $trans_ids);
       }else{
          $sql->where('is_invoice_generated', '=', 0);
       }
       return $sql->whereHas('transType', function($query) use ($invoiceType) { 
            if($invoiceType == 'I') {
                 $query->where('id','=','9');
                 // $query->orWhere('id','=','33');
             }  else {
                $query->where('chrg_master_id','!=','0');
            }
        })->get();
    }
}
