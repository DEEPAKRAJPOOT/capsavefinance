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
        'user_id',
        'trans_date',
        'trans_type',
        'amount',
        'entry_type',
        'gst',
        'cgst',
        'sgst',
        'igst',
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

    public function getsettledAmtAttribute(){
       
        $dr = self::where('parent_trans_id','=',$this->trans_id)
        ->where('entry_type','=','0')
        ->sum('amount');

        $cr = self::where('parent_trans_id','=',$this->trans_id)
        ->where('entry_type','=','1')        
        ->sum('amount');

        return (float)$cr - (float)$dr;
    }

    public function getOutstandingAttribute(){
        return round(($this->amount - $this->getsettledAmtAttribute()),2);
    }

    public function getRefundableAmtAttribute(){
        return self::where('parent_trans_id','=',$this->trans_id)
        ->where('entry_type','=',1)
        ->where('trans_type','=',config('lms.TRANS_TYPE.REFUND'))
        ->sum('amount');

        // return self::where('link_trans_id','=',$this->trans_id)
        // ->where('entry_type','=','0')
        // ->whereIn('trans_type',[config('lms.TRANS_TYPE.REVERSE')]) 
        // ->sum('amount');
    }

    public function getRefundOutstandingAttribute(){
        return round(($this->amount - $this->getRefundableAmtAttribute()),2);
    }

    public function getParentTransDateAttribute(){
        $transDate = '';
        $parentTrans = self::find($this->parent_trans_id);
        if($parentTrans){
            $transDate = $parentTrans->trans_date;
        }
        return $transDate;
    }

    public function getTransNameAttribute(){
        $name = ' '; 
       
        if(in_array($this->trans_type,[config('lms.TRANS_TYPE.WAVED_OFF'),config('lms.TRANS_TYPE.TDS'),config('lms.TRANS_TYPE.REVERSE'),config('lms.TRANS_TYPE.REFUND')])){
            if($this->parent_trans_id){
                $parentTrans = self::find($this->parent_trans_id);
                $name .= $parentTrans->transType->trans_name.' ';
            }
        }

        if($this->entry_type == 0){
            $name .= $this->transType->debit_desc;
        }elseif($this->entry_type == 1){
            $name .= $this->transType->credit_desc;
        }
        return $name;
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
                ->whereNotNull('parent_trans_id')
                ->whereNotIn('trans_type',[config('lms.TRANS_TYPE.REFUND')])
                ->where('user_id','=',$userId)->get()
                ->filter(function($item){
                    return $item->refundoutstanding > 0;
                });
    }

    public static function getRefundTrans($userId){
        return self::where('entry_type','1')
                ->whereNotNull('parent_trans_id')
                ->whereIn('trans_type',[config('lms.TRANS_TYPE.REFUND'),config('lms.TRANS_TYPE.TDS')])
                ->where('user_id','=',$userId)->get();
                //->filter(function($item){
                //    if(in_array($item->trans_type,[config('lms.TRANS_TYPE.REFUND'),config('lms.TRANS_TYPE.TDS')])){
                //        return $item->refundoutstanding > 0;
                //    }
                //    return true;
                //});
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

        $query = self::whereIn('trans_type',[config('lms.TRANS_TYPE.PAYMENT_DISBURSED')])
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

        return $query->get()->filter(function($item) {
            return $item->outstanding > 0;
        });

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
        $invoice = self::where('invoice_disbursed_id','=',$invDesbId)
        ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
        ->whereHas('invoiceDisbursed',function($query){
            $query->whereHas('invoice', function($query){
                $query->whereHas('program_offer',function($query){
                    $query->where('payment_frequency','=',1);
                });
            });
        })
        ->first();

        $intRefund = 0;
        $totalDebitAmt = self::where('entry_type','=','0')
        ->where('invoice_disbursed_id','=',$invDesbId)
        ->whereNotIn('trans_type',[config('lms.TRANS_TYPE.MARGIN')])
        ->sum('amount');
        
        $totalCreditAmt =  self::where('entry_type','=','1')
        ->where('invoice_disbursed_id','=',$invDesbId)
        ->whereNotIn('trans_type',[config('lms.TRANS_TYPE.MARGIN')])
        ->sum('amount');
        $invoice2 = $invoice;

        if($totalDebitAmt <= $totalCreditAmt){
            $invoice = $invoice->accruedInterest();
			if($payment_date){
				$invoice = $invoice->whereDate('interest_date','<',$payment_date);
			}    
            $intRefund = $invoice->sum('accrued_interest');
        }
        
		return collect(['amount'=> $intRefund,'parent_transaction'=>$invoice2]);
    }

    /*** save repayment transaction details for invoice  **/
    public static function saveRepaymentTrans($attr)
    {
          return self::create($attr);
    }
    
    /*** save repayment transaction details for invoice  **/
    public static function saveCharge($attr)
    {
        return self::insert($attr);
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
    










    public static function get_balance($trans_code,$user_id){

        $dr =  self::whereRaw('concat_ws("",user_id, DATE_FORMAT(created_at, "%y%m%d"), (1000000000+trans_id)) <= ?',[$trans_code])
                    ->where('user_id','=',$user_id)
                    ->where('soa_flag','=',1)
                    ->whereNull('payment_id')
                    ->where('entry_type','=','0')
                    ->sum('amount');
                    
        $dr +=  self::whereRaw('concat_ws("",user_id, DATE_FORMAT(created_at, "%y%m%d"), (1000000000+trans_id)) <= ?',[$trans_code])
                    ->where('user_id','=',$user_id)
                    ->where('soa_flag','=',1)
                    ->whereNotNull('payment_id')
                    ->whereIn('trans_type',[config('lms.TRANS_TYPE.INTEREST_OVERDUE'),config('lms.TRANS_TYPE.INTEREST_REFUND')])
                    ->where('entry_type','=','0')
                    ->sum('amount');
                    
        $cr =   self::whereRaw('concat_ws("",user_id, DATE_FORMAT(created_at, "%y%m%d"), (1000000000+trans_id)) <= ?',[$trans_code])
                    ->where('user_id','=',$user_id)
                    ->where('soa_flag','=',1)
                    ->whereNull('payment_id')
                    ->where('entry_type','=','1')
                    ->sum('amount');

        $cr +=  self::whereRaw('concat_ws("",user_id, DATE_FORMAT(created_at, "%y%m%d"), (1000000000+trans_id)) <= ?',[$trans_code])
                    ->where('user_id','=',$user_id)
                    ->where('soa_flag','=',1)
                    ->whereNotNull('payment_id')
                    ->whereIn('trans_type',[config('lms.TRANS_TYPE.INTEREST_OVERDUE'),config('lms.TRANS_TYPE.INTEREST_REFUND')])
                    ->where('entry_type','=','1')
                    ->sum('amount');
        

        return $dr - $cr;
    }
    
    public  function getBalanceAttribute()
    {
        return self::get_balance($this->user_id.Carbon::parse($this->created_at)->format('ymd').(1000000000+$this->trans_id), $this->user_id);
    }

    public static function getUserBalance($user_id){

        $trans = self::select(DB::raw('max(concat_ws("",user_id, DATE_FORMAT(created_at, "%y%m%d"), (1000000000+trans_id)))as trans_code'))->where('user_id','=',$user_id)->get();
        return self::get_balance($trans[0]->trans_code, $user_id);
    }

     /*** get all transaction  **/
    public static function getAllUserChargeTransaction()
    {
          return self::with('user')->where('chrg_trans_id','!=',NULL)->groupBy('user_id')->get();
    }   
     
    /**
     * Update Transaction
     * 
     * @param array $whereCondition
     * @param array $data
     * @return mixed
     */
    public static function updateTransaction($whereCondition, $data)
    {
        return self::where($whereCondition)->update($data);
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

    public function getBatchNoAttribute(){
        if(in_array($this->trans_type ,[config('lms.TRANS_TYPE.REPAYMENT'),config('lms.TRANS_TYPE.PAYMENT_DISBURSED')])){
            return $this->txn_id;
        }
        if(in_array($this->trans_type ,[config('lms.TRANS_TYPE.INTEREST_REFUND'), config('lms.TRANS_TYPE.NON_FACTORED_AMT'), config('lms.TRANS_TYPE.MARGIN') ]) && !$this->payment_id && $this->refundTransaction != null){
            return $this->refundTransaction->request->batch->batch_id;
        }
    }

    public function getNarrationAttribute(){
        $data = '';
        if($this->trans_type == config('lms.TRANS_TYPE.REPAYMENT'))
        $data .= $this->BatchNo.' ';

        if($this->modeOfPaymentName && $this->modeOfPaymentNo)
        $data .= $this->modeOfPaymentName.': '.$this->modeOfPaymentNo.' ';

        if($this->trans_type == config('lms.TRANS_TYPE.REPAYMENT'))
        $data .= ' Repayment Allocated as Normal: '.$this->amount . ' TDS:0.00'.' ';

        if($this->is_tds && $this->tds_cert)
        $data .= ' TDS CERT No: '.$this->tds_cert ;

        return $data;
    }

    public static function getSoaList(){

        return self::select('transactions.*')
                    ->join('users', 'transactions.user_id', '=', 'users.user_id')
                    ->join('lms_users','users.user_id','lms_users.user_id')
                    //->where('soa_flag','=',1)
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

    
    public static function getAllChargesApplied(array $where = array()) {
        $cond = '';
        if (!empty($where)) {
            foreach ($where as $key => $value) {
                $wh[] = "t1.$key = '$value'";
            }
           $cond = ' AND ' .implode(' AND ', $wh);
        }
        dd($cond);
        $query = "SELECT DATE_FORMAT(t1.trans_date, '%d/%m/%Y') as trans_date, t1.trans_id, t1.parent_trans_id, t1.trans_name, t1.trans_desc, t1.user_id, t1.entry_type, t1.amount AS debit_amount, IFNULL(SUM(t2.amount), 0) as credit_amount, (t1.amount - IFNULL(SUM(t2.amount), 0)) as remaining 
        FROM `get_all_charges` t1 
        LEFT JOIN rta_transactions as t2 ON t1.trans_id = t2.parent_trans_id 
        WHERE t1.entry_type = 0  ". $cond ." GROUP BY t1.trans_id HAVING remaining > 0";
        $result = \DB::SELECT(\DB::raw($query));
        return $result;
    }
    
    public static function getTallyTxns(array $where = array()) {
        $result = self::select('transactions.trans_id', 'transactions.payment_id repay_trans_id', 'transactions.parent_trans_id', 'transactions.user_id', 'users.f_name', 'users.m_name', 'users.l_name', 'transactions.biz_id', 'transactions.virtual_acc_id', 'transactions.disbursal_id', 'transactions.trans_date', 'transactions.trans_type', 'transactions.chrg_trans_id', 'transactions.amount', 'transactions.settled_amount', 'transactions.entry_type', 'user_bank_account.acc_name', 'user_bank_account.acc_no', 'mst_bank.bank_name', 'user_bank_account.ifsc_code' , 'transactions.is_settled', 'transactions.mode_of_pay', 'transactions.utr_no', 'transactions.unr_no', 'transactions.cheque_no', 'transactions.trans_by', 'transactions.pay_from', 'transactions.txn_id', 'transactions.is_posted_in_tally', 'transactions.comment', 'tally_voucher.trans_type_id', 'mst_trans_type.trans_name', 'mst_trans_type.credit_desc', 'mst_trans_type.debit_desc', 'mst_trans_type.tally_trans_type', 'tally_voucher.tally_voucher_id', 'tally_voucher.voucher_name', 'tally_voucher.created_at as voucher_date')
            ->join('users', 'users.user_id', '=', 'transactions.user_id')
            ->join('mst_trans_type', 'mst_trans_type.id', '=', 'transactions.trans_type')
            ->join('tally_voucher', 'tally_voucher.trans_type_id', '=', 'mst_trans_type.id')
            ->join('user_bank_account', 'user_bank_account.user_id', '=', 'transactions.user_id')
            ->join('mst_bank', 'mst_bank.id', '=', 'user_bank_account.bank_id')
            ->where($where)
            ->groupBy('transactions.trans_id')
            ->get();
        return $result;
    }
}
