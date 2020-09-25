<?php
namespace App\Inv\Repositories\Models\Lms;

use DB;
use Helpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Inv\Repositories\Models\Master\Tds;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\Lms\InterestAccrualTemp;
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
    public $timestamps = true;

    /**
     * Maintain created_by and updated_by automatically
     *
     * @var boolean
     */
    public $userstamps = true;

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
        'trans_mode',
        'amount',
        'entry_type',
        'gst',
        'gst_per',
        'chrg_gst_id',
        'tds_per',
        'gl_flag',
        'soa_flag',
        'trans_by',
        'pay_from',
        'is_settled',
        'is_posted_in_tally',
        'is_invoice_generated',
        'sys_created_at',
        'sys_updated_at',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    public function childTransactions(){
        return $this->belongsTo('App\Inv\Repositories\Models\Lms\Transactions', 'trans_id', 'parent_trans_id');
    }

    public function parentTransactions(){
        return $this->hasOne('App\Inv\Repositories\Models\Lms\Transactions','trans_id','parent_trans_id');
    }

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
        return $this->hasOne('App\Inv\Repositories\Models\Lms\Refund\RefundReqTrans','trans_id','trans_id');
    }

    public function transRunning(){
        return $this->belongsTo('App\Inv\Repositories\Models\Lms\TransactionsRunning','trans_running_id','trans_running_id');
    }

    public function tallyEntry(){
        return $this->belongsTo('App\Inv\Repositories\Models\Master\TallyEntry','trans_id','transactions_id');
    }

    public function ChargesTransactions(){
        return $this->hasOne('App\Inv\Repositories\Models\Lms\ChargesTransactions','trans_id','trans_id');
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
        ->whereNotIn('trans_type',[config('lms.TRANS_TYPE.REFUND'),config('lms.TRANS_TYPE.ADJUSTMENT')])
        ->sum('amount');

        $cr = self::where('parent_trans_id','=',$this->trans_id)
        ->where('entry_type','=','1')        
        ->sum('amount');

        return (float)$cr - (float)$dr;
    }

    public function getOutstandingAttribute(){
        return round(($this->amount - $this->getsettledAmtAttribute()),2);
    }

    public function getRevertedAmtAttribute(){
        return self::where('link_trans_id','=',$this->trans_id)
        ->whereIn('trans_type',[config('lms.TRANS_TYPE.REVERSE'),config('lms.TRANS_TYPE.CANCEL')])
        ->where(function($query){
            $query->where(function($q){
                $q->where('entry_type','=','0');
                $q->where('trans_type','=', config('lms.TRANS_TYPE.REVERSE'));
            });
            $query->orWhere(function($q){
                $q->where('entry_type','=','1');
                $q->where('trans_type','=', config('lms.TRANS_TYPE.CANCEL'));
            });
        })
        ->sum('amount');
    }

    public function getIsTransactionAttribute(){
        $flag = true;

        if($this->parentTransactions){
            if($this->parentTransactions->transType->chrg_master_id && !$this->parentTransactions->is_invoice_generated){
                $flag = false;
            }
        }else{
            if($this->transType->chrg_master_id && !$this->is_invoice_generated){
                $flag = false;
            }
        }

        return $flag;
    }
    
    public function getSettledOutstandingAttribute(){
        return round(($this->amount - $this->getRevertedAmtAttribute()),2);
    }

    public function getDpdAttribute(){
        $to = Carbon::createFromFormat('Y-m-d H:i:s', Helpers::getSysStartDate());
        if($this->trans_type == config('lms.TRANS_TYPE.PAYMENT_DISBURSED')){
            $from = Carbon::createFromFormat('Y-m-d', $this->invoiceDisbursed->payment_due_date);
        }
        elseif($this->trans_type == config('lms.TRANS_TYPE.INTEREST')){
            $from = Carbon::createFromFormat('Y-m-d H:i:s', $this->trans_date);
        }

        return $to->diffInDays($from);
    }

    public function getWaiveOffAmount(){
        return self::where(['parent_trans_id' => $this->trans_id, 'trans_type' => config('lms.TRANS_TYPE.WAVED_OFF')])->sum('amount');
    }

    public function getRefundableAmtAttribute(){
        return self::where('link_trans_id','=',$this->trans_id)
        ->where('entry_type','=','0')
        ->whereIn('trans_type',[config('lms.TRANS_TYPE.REVERSE'),config('lms.TRANS_TYPE.REFUND'),config('lms.TRANS_TYPE.ADJUSTMENT')]) 
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
        if($this->trans_type == config('lms.TRANS_TYPE.REPAYMENT')) 
        return $this->payment->paymentname;

        if(in_array($this->trans_type,[config('lms.TRANS_TYPE.WRITE_OFF'),config('lms.TRANS_TYPE.WAVED_OFF'),config('lms.TRANS_TYPE.TDS'),config('lms.TRANS_TYPE.REVERSE'),config('lms.TRANS_TYPE.REFUND'),config('lms.TRANS_TYPE.CANCEL'),config('lms.TRANS_TYPE.ADJUSTMENT'),config('lms.TRANS_TYPE.NON_FACTORED_AMT')])){
            if($this->parent_trans_id){
                $parentTrans = self::find($this->parent_trans_id);
                if($parentTrans->entry_type == 0){
                    $name .= ' '.$parentTrans->transType->debit_desc;
                }elseif($parentTrans->entry_type == 1){
                    $name .= ' '.$parentTrans->transType->credit_desc;
                }
                if($this->link_trans_id){
                    $linkTrans = self::find($this->link_trans_id);
                    if($linkTrans){
                        if(in_array($linkTrans->trans_type,[config('lms.TRANS_TYPE.WRITE_OFF'),config('lms.TRANS_TYPE.WAVED_OFF'),config('lms.TRANS_TYPE.TDS'),config('lms.TRANS_TYPE.REVERSE'),config('lms.TRANS_TYPE.CANCEL'),config('lms.TRANS_TYPE.ADJUSTMENT')]))
                        if($linkTrans->entry_type == 0){
                            $name .= ' '.$linkTrans->transType->debit_desc;
                        }elseif($linkTrans->entry_type == 1){
                            $name .= ' '.$linkTrans->transType->credit_desc;
                        }    
                    }
                }
            }
        }

        if($this->entry_type == 0){
            $name .= ' '.$this->transType->debit_desc;
        }elseif($this->entry_type == 1){
            $name .= ' '.$this->transType->credit_desc;
        }
        return trim($name);
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
        if(in_array($this->trans_type,[config('lms.TRANS_TYPE.REPAYMENT')])){
            if($this->BatchNo)
            $data .= $this->BatchNo.' ';
            if($this->payment->transactionno)
            $data .= $this->payment->paymentmode.': '.$this->payment->transactionno.' ' ;
            $data .= ' Payment Allocated as Normal: INR '. number_format($this->payment->amount,2) . ' ';
        }
        if(in_array($this->trans_type,[config('lms.TRANS_TYPE.FAILED')])){
            $data .= ' Payment Failed as Normal: INR '. number_format($this->payment->amount,2) . ' ';
        }
        return trim($data);
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

    public function getSoaBackgroundColorAttribute(){
        $color = '';
        if($this->payment_id){
            if($this->trans_type == config('lms.TRANS_TYPE.REPAYMENT'))
            $color = '#f3c714';
            elseif(!in_array($this->trans_type, [config('lms.TRANS_TYPE.TDS')]))
            $color = '#ffe787';
        }
        return $color;
    }
    
    public  function getBalanceAttribute()
    {
        return self::get_balance($this->user_id.Carbon::parse($this->created_at)->format('ymd').(1000000000+$this->trans_id), $this->user_id);
    }

    public  function getPaymentDueDateAttribute()
    {
        if(in_array($this->trans_type,[config('lms.TRANS_TYPE.PAYMENT_DISBURSED'),config('lms.TRANS_TYPE.MARGIN')])){
            return Carbon::parse($this->invoiceDisbursed->payment_due_date)->format('Y-m-d');
        }elseif($this->invoiceDisbursed && $this->invoiceDisbursed->invoice->program_offer->payment_frequency == 1  && $this->trans_type == config('lms.TRANS_TYPE.INTEREST')){
            return Carbon::parse($this->invoiceDisbursed->payment_due_date)->format('Y-m-d');
        }else{
            return Carbon::parse($this->trans_date)->format('Y-m-d');
        }
    }

    public function getTDSAmountAttribute(){
        $amount = $this->amount;
        $baseAmt = 0;
        $tdsAmt = 0;
        $gstAmt = 0;
        $gst_per = 0;
        if($this->transType->chrg_master_id || $this->trans_type == config('lms.TRANS_TYPE.INTEREST')){
            $tdsRate = Tds::getActiveTdsBaseRate($this->trans_date);
            $amount -= $this->getWaiveOffAmount();
            
            if($this->transType->chrg_master_id){
                $gst_per = $this->transType->charge->gst_percentage;
            }
            
            if($gst_per){
                $baseAmt = $amount*100/(100+$gst_per);
            }else{
                $baseAmt = $amount;
            }
            
            $tdsAmt = $baseAmt*$tdsRate/100;
        }
        
        $tds = self::where('parent_trans_id',$this->trans_id)
        ->where('trans_type', config('lms.TRANS_TYPE.TDS'))
        ->where('entry_type','1')
        ->get();   
        foreach($tds as $tdsTrans){
            $tdsAmt -= $tdsTrans->settledOutstanding;
        }

        if($tdsAmt < 0 ){
            $tdsAmt = 0;
        }
        return $tdsAmt;
    }

    public static function getUnsettledTrans($userId){
        return self::whereNull('parent_trans_id')
                ->whereNull('payment_id')
                ->where('user_id',$userId)
                ->get()
                ->filter(function($item) {
                    return $item->outstanding > 0;
                });
    }

    public static function getUserOutstanding($userId){
        $trans = self::getUnsettledTrans($userId);
    }

    public static function getSettledTrans($userId){
        return self::where('entry_type','1')
                //->whereNotNull('parent_trans_id')
                ->whereNotIn('trans_type',[config('lms.TRANS_TYPE.REFUND'),config('lms.TRANS_TYPE.REVERSE'),config('lms.TRANS_TYPE.NON_FACTORED_AMT')])
                ->where('user_id','=',$userId)->get()
                ->filter(function($item){
                    return ($item->refundoutstanding > 0 && $item->IsTransaction);
                });
    }

    public static function getRefundTrans($userId){
        return self::where('entry_type','1')
                //->whereNotNull('parent_trans_id')
                ->whereIn('trans_type',[config('lms.TRANS_TYPE.REFUND'),config('lms.TRANS_TYPE.TDS'),config('lms.TRANS_TYPE.MARGIN'),config('lms.TRANS_TYPE.NON_FACTORED_AMT')])
                ->where('user_id','=',$userId)->get()
                ->filter(function($item){
                    if($item->refundReqTrans){
                        return false;
                    }
                    if($item->trans_type == config('lms.TRANS_TYPE.TDS') && $item->payment->is_refundable == 0){
                        return false;
                    }
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
        
        $transactions['sys_updated_at'] = Helpers::getSysStartDate();
        if (!empty($whereCondition)) {
            return self::where($whereCondition)->update($transactions);
        }else{
            $transactions['sys_created_at'] = Helpers::getSysStartDate();
            return self::create($transactions);
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

        if(isset($data['invoice_id'])){
            $query->whereHas('invoiceDisbursed', function($q) use($data){
                $q->where('invoice_id','=',$data['invoice_id']);
            });
        }
        
        if(isset($data['intAccrualStartDateLteSysDate'])){
            $query->wherehas('invoiceDisbursed',function($q){
                $start = new \Carbon\Carbon(\Helpers::getSysStartDate());
                $sysStartDate = $start->format('Y-m-d');
                $q->whereDate('int_accrual_start_dt','<=',$sysStartDate);
            });
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
            $query = $query->where('invoice_disbursed_id',$data['invoice_disbursed_id']);
        }

        if(isset($data['user_id'])){
            $query = $query->where('user_id',$data['user_id']);
        }

        if(isset($data['trans_type']) && !empty($data['trans_type'])){
            $query = $query->whereIn('trans_type',$data['trans_type']);
        }

        if(!empty($data['trans_type_not_in'])){
            $query->whereNotIn('trans_type',$data['trans_type_not_in']);
        }

        $query = $query->orderByRaw("FIELD(trans_type, '".config('lms.TRANS_TYPE.INTEREST')."', '".config('lms.TRANS_TYPE.PAYMENT_DISBURSED')."', '".config('lms.TRANS_TYPE.INTEREST_OVERDUE')."', '".config('lms.TRANS_TYPE.MARGIN')."' )");
        $query = $query->orderby('trans_date','asc');
        return $query->get()->filter(function($item) {
            return ($item->outstanding > 0 && $item->IsTransaction);
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
            return ($item->outstanding > 0 && $item->IsTransaction);
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

    public function getReversalParent() {
        return $this->belongsTo('App\Inv\Repositories\Models\Payment', 'trans_id', 'parent_trans_id');
    }

    public static function postedTxnsInTally() {
        return self::where('is_posted_in_tally', 1)->get();
    }

    public static function getJournalTxnTally(array $where = []){
        return self::with('transType')->where(function ($query) {
            $query->whereHas('transType', function($q) { 
                $q->where('entry_type', '=', '0')->where('is_invoice_generated', '=', '1')->where(function ($qry) {
                   $qry->where('id', '=', config('lms.TRANS_TYPE.INTEREST'))->orWhere('chrg_master_id','!=','0');
                });
            })
            ->orWhereIn('trans_type', [config('lms.TRANS_TYPE.REVERSE'),config('lms.TRANS_TYPE.WAVED_OFF')])
            ->orWhere(function ($q) {
                $q->whereIn('trans_type', [config('lms.TRANS_TYPE.REFUND')])->whereNotNull('parent_trans_id')->where('entry_type', '=', '1');
            })
            ->orWhere(function ($q) {
                $q->whereIn('trans_type', [config('lms.TRANS_TYPE.MARGIN'), config('lms.TRANS_TYPE.NON_FACTORED_AMT'), config('lms.TRANS_TYPE.TDS')])->where('entry_type', '=', '1');
            });
        })->where($where)->orderBy('trans_date', 'ASC')->get()->filter(function($item){
            return ( $item->IsTransaction);
}); 
    }

    public static function getDisbursalTxnTally(array $where = []){
        return self::whereIn('trans_type', [config('lms.TRANS_TYPE.PAYMENT_DISBURSED')])
            ->where('entry_type', '=', 0)
            ->where($where)
            ->get();
    }

    public static function getRefundTxnTally(array $where = []){
        return self::whereIn('trans_type', [config('lms.TRANS_TYPE.REFUND')])
            ->where('entry_type', '=', 0)
            ->where($where)
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
    public static function saveRepaymentTrans($transactions)
    {
        $transactions['sys_updated_at'] = Helpers::getSysStartDate();
        $transactions['sys_created_at'] = Helpers::getSysStartDate();
        return self::create($transactions);
    }
    
    /*** save repayment transaction details for invoice  **/
    public static function saveCharge($transactions)
    {
        $transactions['sys_updated_at'] = Helpers::getSysStartDate();
        $transactions['sys_created_at'] = Helpers::getSysStartDate();
        return self::create($transactions);
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

    public static function getConsolidatedSoaList(){
        return self::select('transactions.*')
                    ->orderBy('user_id', 'asc')
                    ->orderBy(DB::raw("DATE_FORMAT(rta_transactions.created_at, '%Y-%m-%d')"), 'asc')
                    ->orderBy('trans_id', 'asc');
    }

    public static function getSoaList(){

        return self::select('transactions.*')
                    ->where('soa_flag','=',1)
                    ->orderBy('user_id', 'asc')
                    ->orderBy(DB::raw("DATE_FORMAT(rta_transactions.created_at, '%Y-%m-%d')"), 'asc')
                    ->orderBy('trans_id', 'asc');
    }

    public static function getColenderSoaList(){
        return self::select('transactions.*', 'co_lenders_share.capsave_percent', 'co_lenders_share.co_lender_percent','co_lenders_share.start_date', 'co_lenders_share.end_date')
                    ->leftJoin('co_lenders_share', function ($join){
                        $join->on('transactions.user_id', '=' ,'co_lenders_share.user_id')
                        ->whereRaw('trans_date >= start_date AND end_date is null AND is_active = 1')
                        ->orWhereRaw('trans_date between start_date and end_date');
                    })
                    ->where('soa_flag','=',1)
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
        return self::where($where)
        ->whereNull('parent_trans_id')
        ->whereNull('link_trans_id')
        ->whereNull('payment_id')
        ->where('entry_type',0)
        ->get()
        ->filter(function($item) {
            return ($item->IsTransaction);
        });
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
             }  else {
                $query->where('chrg_master_id','!=','0');
            }
        })->get();
    }
    
    /**
     * Get Disbursal transactions
     * 
     * @param string $transStartDate
     * @param string $transEndDate
     * 
     * @return mixed
     */
    public static function checkDisbursalTrans($transStartDate, $transEndDate)
    {
        $query = self::select('transactions.*', 'invoice_disbursed.disbursal_id', 'invoice_disbursed.invoice_id', 'app_prgm_offer.payment_frequency')
                ->join('invoice_disbursed', 'invoice_disbursed.invoice_disbursed_id', '=', 'transactions.invoice_disbursed_id')
                ->join('invoice', 'invoice_disbursed.invoice_id', '=', 'invoice.invoice_id')
                ->join('app_prgm_offer', 'app_prgm_offer.prgm_offer_id', '=', 'invoice.prgm_offer_id')
                ->whereBetween('trans_date', [$transStartDate, $transEndDate])
                ->whereIn('trans_type', [config('lms.TRANS_TYPE.PAYMENT_DISBURSED'), config('lms.TRANS_TYPE.MARGIN'), config('lms.TRANS_TYPE.INTEREST')])
                ->whereNull('parent_trans_id')
                ->whereNull('link_trans_id')
                ->whereNull('payment_id')
                ->where('entry_type', '0');
                
        $result = $query->get();
        
        return $result;
    }  
    
    public static function checkRunningTrans($transStartDate, $transEndDate){
        return self::whereBetween('trans_date', [$transStartDate, $transEndDate])
        ->whereIn('trans_type',[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')])
        ->whereNotNull('trans_running_id')
        ->get()
        ->filter(function($item) {
            return $item->outstanding > 0;
        });

    }

    public static function getDishonouredTxn($user_id) {
        return self::where(['user_id' => $user_id, 'trans_type' => config('lms.CHARGE_TYPE.CHEQUE_BOUNCE')])->get();
    }    
 
    
    public static function getUserLimitOutstanding($attr)
    {
        $userId = $attr->user_id;
        $disbursedList = self::whereNull('parent_trans_id')
        ->whereNull('payment_id')
        ->where('entry_type','0')
        ->whereIn('trans_type',[config('lms.TRANS_TYPE.PAYMENT_DISBURSED')])
        ->where('user_id',$userId)
        ->get()
        ->filter(function($item) {
            return $item->outstanding > 0;
        });

        $interestList = self::whereNull('parent_trans_id')
        ->whereNull('payment_id')
        ->where('entry_type','0')
        ->whereIn('trans_type',[config('lms.TRANS_TYPE.INTEREST')])
        ->where('user_id',$userId)
        ->get()
        ->filter(function($item) {
            return $item->outstanding > 0;
        });

        $outstandingAmt = 0;
        $outstandingPrincipalAmt = 0;

        foreach($disbursedList as $tran){
            $outstandingAmt += $tran->outstanding;
            $outstandingPrincipalAmt += $tran->outstanding;
        }

        foreach($interestList as $tran){
            $outstandingAmt += $tran->outstanding;
        }
        if($attr->chrg_applicable_id==2)
        {    
            return round($outstandingAmt,2);
        }
        if($attr->chrg_applicable_id==3)
        {
           return round($outstandingPrincipalAmt,2); 
        }
    }
    
    public function getFromIntDateAttribute(){
        $fromDate = null;
        if(in_array($this->trans_type,[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')])){
            $fromDate = self::where('invoice_disbursed_id',$this->invoice_disbursed_id)
            ->whereDate('trans_date','<=',$this->trans_date)
            ->where('trans_id','<',$this->trans_id)
            ->whereIn('trans_type',[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')])
            ->whereNull('link_trans_id')
            ->whereNull('parent_trans_id')
            ->where('entry_type','0')
            ->max('trans_date');
            
            if(!$fromDate){
                $fromDate = $this->disburse->int_accrual_start_dt;
            }else{
                $fromDate = date('Y-m-d', strtotime($fromDate . "+ 1 days"));
            }
        }
        return $fromDate;
    }

    public function getToIntDateAttribute(){
        $toDate = null;
        if(in_array($this->trans_type,[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')])){
            if($this->invoiceDisbursed->invoice->program_offer->payment_frequency == 1){
                $toDate = $this->invoiceDisbursed->payment_due_date;
            }else{
                $toDate = $this->trans_date;
            }
        }
        return $toDate;
    }

    public function getTempInterestAttribute(){
        $amount = null;
        $from = self::getFromIntDateAttribute();
        $to = self::getToIntDateAttribute();
        $outstanding = self::getOutstandingAttribute();
        $invoice_disbursed_id = $this->invoice_disbursed_id;
        if($from && $to && $invoice_disbursed_id && in_array($this->trans_type,[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')])){
            $amount = InterestAccrualTemp::whereDate('interest_date','>=',$from)
            ->whereDate('interest_date','<=',$to)
            ->where('invoice_disbursed_id',$invoice_disbursed_id)
            ->sum('accrued_interest');   
            if($amount <= $outstanding){
                $amount = round($amount,2);
            }else{
                $amount = round($outstanding,2);
            }

        }
        return $amount;
    }
    
    public static function getInterestBreakupReport($whereCondition=[], $whereRawCondition = NULL){
        $data = [];

        $disbTrans = self::where('trans_type', config('lms.TRANS_TYPE.PAYMENT_DISBURSED'))
        ->whereNull('parent_trans_id')
        ->where('entry_type','0')
        ->get();

        foreach($disbTrans as $dTrans){
            $unIntTrans = self::where('trans_type', config('lms.TRANS_TYPE.INTEREST'))
            ->where('invoice_disbursed_id', $dTrans->invoice_disbursed_id)
            ->whereNull('parent_trans_id')
            ->where('entry_type','0')
            ->get();

            foreach($unIntTrans as $uITrans){
                $data[$uITrans->trans_id] = 
                [
                    'loan' => config('common.idprefix.APP').$uITrans->invoiceDisbursed->invoice->app_id,
                    'client_name' => $uITrans->user->f_name.' '.$uITrans->user->l_name,
                    'disbursed_amt' => $dTrans->amount,
                    'from_date' => $uITrans->fromIntDate,
                    'to_date' => $uITrans->toIntDate,
                    'days' => abs(round((strtotime($uITrans->toIntDate) - strtotime($uITrans->fromIntDate)) / 86400)),
                    'int_rate' => $uITrans->invoiceDisbursed->interest_rate,
                    'int_amt' => $uITrans->amount,
                    'collection_date' => null,
                    'tds_rate' => null,
                    'tds_amt' => 0,
                    'net_int' => 0,
                    'tally_batch' => ''
                ];
                $data[$uITrans->trans_id]['collection_date'] = self::where('trans_type', config('lms.TRANS_TYPE.INTEREST'))
                ->where('invoice_disbursed_id', $uITrans->invoice_disbursed_id)
                ->where('parent_trans_id', $uITrans->trans_id)
                ->where('entry_type','1')
                ->max('trans_date');

                $data[$uITrans->trans_id]['tds_amt'] = self::where('trans_type', config('lms.TRANS_TYPE.TDS'))
                ->whereNotNull('payment_id')
                ->where('invoice_disbursed_id', $uITrans->invoice_disbursed_id)
                ->where('parent_trans_id', $uITrans->trans_id)
                ->where('entry_type','1')
                ->sum('amount');

                $tdsRates = self::where('trans_type', config('lms.TRANS_TYPE.TDS'))
                ->whereNotNull('payment_id')
                ->where('invoice_disbursed_id', $uITrans->invoice_disbursed_id)
                ->where('parent_trans_id', $uITrans->trans_id)
                ->where('entry_type','1')
                ->whereNotNull('tds_per')
                ->pluck('tds_per')
                ->toArray();
                
                $data[$uITrans->trans_id]['tds_rate'] = implode(',', $tdsRates);

                $data[$uITrans->trans_id]['net_int'] = $data[$uITrans->trans_id]['int_amt'] - $data[$uITrans->trans_id]['tds_amt'];

                $tallyEntries =  $uITrans->tallyEntry;

                if($tallyEntries){
                    $tallyEntries = $tallyEntries->first();
                    $data[$uITrans->trans_id]['tally_batch'] = $tallyEntries->batch_no;
                }
            }
        }
        return $data;
    }

    public static function getchargeBreakupReport($whereCondition=[], $whereRawCondition = NULL){
        $data = [];

        $chargTrans = self::whereHas('transType', function($query){
            $query->where('chrg_master_id','>','0');
        })
        ->whereNull('parent_trans_id')
        ->where('entry_type','0')
        ->get();
        foreach($chargTrans as $cTrans){
            
            $data[$cTrans->trans_id] = 
            [
                'loan' => '',
                'client_name' =>$cTrans->user->f_name.' '.$cTrans->user->l_name,
                'chrg_name' => $cTrans->transName,
                'chrg_rate' => '',
                'chrg_amt' => '',
                'gst' => '',
                'net_amt' => $cTrans->amount, 
                'tally_batch' => ''
            ];

            if($cTrans->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')){
                $data[$cTrans->trans_id]['loan'] = config('common.idprefix.APP').$cTrans->invoiceDisbursed->invoice->app_id;
                $data[$cTrans->trans_id]['chrg_amt'] = $cTrans->amount;
                $data[$cTrans->trans_id]['chrg_rate'] = $cTrans->invoiceDisbursed->interest_rate;
            }else{
                $charge = $cTrans->chargesTransactions;
                if($charge){
                    $data[$cTrans->trans_id]['loan'] = $charge->app_id?config('common.idprefix.APP').$charge->app_id:'';
                    $data[$cTrans->trans_id]['chrg_amt'] = $charge->amount;
                    $data[$cTrans->trans_id]['chrg_rate'] = ($charge->chargeMaster->chrg_calculation_type == 2)?$charge->percent:'';
                }
            }

            if($cTrans->userInvTrans){
                $data[$cTrans->trans_id]['gst'] = $cTrans->userInvTrans->sgst_amount + $cTrans->userInvTrans->cgst_amount + $cTrans->userInvTrans->igst_amount;
            }
            $tallyEntries =  $cTrans->tallyEntry;
            if($tallyEntries){
                $tallyEntries = $tallyEntries->first();
                $data[$cTrans->trans_id]['tally_batch'] = $tallyEntries->batch_no;
            }
        }
        return $data;
    }
    
    public static function gettdsBreakupReport($whereCondition=[], $whereRawCondition = NULL){
        $data = [];

        $chargTrans = self::where('trans_type', config('lms.TRANS_TYPE.INTEREST'))
        ->whereNull('parent_trans_id')
        ->where('entry_type','0')
        ->get();

        
        foreach($chargTrans as $cTrans){

            $tdsTrans = self::where('trans_type', config('lms.TRANS_TYPE.TDS'))
            ->whereNotNull('payment_id')
            ->where('invoice_disbursed_id', $cTrans->invoice_disbursed_id)
            ->where('parent_trans_id', $cTrans->trans_id)
            ->where('entry_type','1')
            ->get();

            foreach($tdsTrans as $tds){

                $data[$tds->trans_id] = 
                [
                    'loan' => '',
                    'client_name' => $tds->user->f_name.' '.$tds->user->l_name,
                    'int_amt' => $cTrans->amount,
                    'deduction_date' => $cTrans->trans_date,
                    'tds_amt' => $tds->amount,
                    'tds_certificate' => $tds->payment->tds_certificate_no,
                    'tally_batch' => ''
                ];
                if($cTrans->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')){
                    $data[$cTrans->trans_id]['loan'] = config('common.idprefix.APP').$cTrans->invoiceDisbursed->invoice->app_id;
                }else{
                    $charge = $cTrans->chargesTransactions;
                    if($charge){
                        $data[$cTrans->trans_id]['loan'] = $charge->app_id?config('common.idprefix.APP').$charge->app_id:'';
                    }
                }
                $tallyEntries =  $tds->tallyEntry;
                if($tallyEntries){
                    $tallyEntries = $tallyEntries->first();
                    $data[$tds->trans_id]['tally_batch'] = $tallyEntries->batch_no;
                }
            }
        }
        return $data;
    }
    
    public function getInterestForDisbursal(array $where = []) {
      return $this->belongsTo('App\Inv\Repositories\Models\Lms\Transactions', 'invoice_disbursed_id', 'invoice_disbursed_id')->where($where)->first();
    }
}
