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
        'parent_trans_id',
        'repay_trans_id',
        'gl_flag',
        'soa_flag',
        'user_id',
        'biz_id',
        'chrg_trans_id',
        'virtual_acc_id',
        'disbursal_id',
        'trans_date',
        'trans_type',
        'trans_by',
        'pay_from',
        'amount',
        'settled_amount',
        'gst',
        'cgst',
        'sgst',
        'igst',
        'entry_type',
        'tds_per',
        'mode_of_pay',
        'comment',
        'utr_no',
        'cheque_no',
        'txn_id',        
        'created_at',
        'created_by',
    ];

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
            $transactions['created_at'] = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
        }
        if (!isset($transactions['created_by'])) {
            $transactions['created_at'] = \Auth::user()->user_id;
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
          return self::with(['biz','disburse','trans_detail','user'])->where('trans_by','!=',NULL)->orderBy('trans_id','DESC');
    }
    
    public function disburse()
    {
       return $this->hasOne('App\Inv\Repositories\Models\Lms\Disbursal','disbursal_id','disbursal_id');
    }      
    
    public function trans_detail()
    {
       return $this->hasOne('App\Inv\Repositories\Models\Lms\TransType', 'id', 'trans_type');
    }   

    public function user(){
        return $this->belongsTo('App\Inv\Repositories\Models\User','user_id','user_id');
    }

    public static function get_balance($trans_code,$user_id){

        $dr =  self::whereRaw('concat_ws("",user_id, DATE_FORMAT(created_at, "%y%m%d"), (1000000000+trans_id)) <= ?',[$trans_code])
                    ->where('user_id','=',$user_id)
                    ->where('soa_flag','=',1)
                    ->whereNull('repay_trans_id')
                    ->where('entry_type','=','0')
                    ->sum('amount');
                    
        $dr +=  self::whereRaw('concat_ws("",user_id, DATE_FORMAT(created_at, "%y%m%d"), (1000000000+trans_id)) <= ?',[$trans_code])
                    ->where('user_id','=',$user_id)
                    ->where('soa_flag','=',1)
                    ->whereNotNull('repay_trans_id')
                    ->whereIn('trans_type',[config('lms.TRANS_TYPE.INTEREST_OVERDUE'),config('lms.TRANS_TYPE.INTEREST_REFUND')])
                    ->where('entry_type','=','0')
                    ->sum('amount');
                    
        $cr =   self::whereRaw('concat_ws("",user_id, DATE_FORMAT(created_at, "%y%m%d"), (1000000000+trans_id)) <= ?',[$trans_code])
                    ->where('user_id','=',$user_id)
                    ->where('soa_flag','=',1)
                    ->whereNull('repay_trans_id')
                    ->where('entry_type','=','1')
                    ->sum('amount');

        $cr +=  self::whereRaw('concat_ws("",user_id, DATE_FORMAT(created_at, "%y%m%d"), (1000000000+trans_id)) <= ?',[$trans_code])
                    ->where('user_id','=',$user_id)
                    ->where('soa_flag','=',1)
                    ->whereNotNull('repay_trans_id')
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

    /** 
       * @Author: Rent Alpha
       * @Date: 2020-02-20 10:53:40 
       * @Desc:  function for get user details from lms user table using user id 
       */      
    public function lmsUser()
    {
       return $this->hasOne('App\Inv\Repositories\Models\LmsUser', 'user_id', 'user_id');
    }

    /**
    * Get Transaction Type and Charge Name 
    */
    public function getTransNameAttribute(){
        if($this->trans_detail->chrg_master_id!='0'){
            if($this->entry_type == 0){
                return $this->trans_detail->charge->debit_desc;
            }elseif($this->entry_type == 1){
                return $this->trans_detail->charge->credit_desc;
            }
        }else{
            if($this->entry_type == 0){
                return $this->trans_detail->debit_desc;
            }elseif($this->entry_type == 1){
                return $this->trans_detail->credit_desc;
            }
        }
    }

    public function getOppTransNameAttribute(){
        if($this->trans_detail->chrg_master_id!='0'){
            if($this->entry_type == 0){
                return $this->trans_detail->charge->credit_desc;
            }elseif($this->entry_type == 1){
                return $this->trans_detail->charge->debit_desc;
            }
        }else{
            if($this->entry_type == 0){
                return $this->trans_detail->credit_desc;
            }elseif($this->entry_type == 1){
                return $this->trans_detail->debit_desc;
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
        if(in_array($this->trans_type ,[config('lms.TRANS_TYPE.REPAYMENT'),config('lms.TRANS_TYPE.PAYMENT_DISBURSED')]))
        return $this->txn_id;
    }

    public function getNarrationAttribute(){
        $data = '';
        if($this->trans_type == config('lms.TRANS_TYPE.REPAYMENT'))
        $data .= $this->BatchNo.' ';

        if($this->modeOfPaymentName && $this->modeOfPaymentNo)
        $data .= $this->modeOfPaymentName.': '.$this->modeOfPaymentNo.' ';

        if($this->trans_type == config('lms.TRANS_TYPE.REPAYMENT'))
        $data .= ' Repayment Allocated as Normal: '.$this->amount . ' TDS:0.00'.' ';
        return $data;
    }

    public static function getSoaList(){

        return self::select('transactions.*')
                    ->join('users', 'transactions.user_id', '=', 'users.user_id')
                    ->join('lms_users','users.user_id','lms_users.user_id')
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

 public function biz(){
        return $this->belongsTo('App\Inv\Repositories\Models\Business','biz_id','biz_id');
    }
}
