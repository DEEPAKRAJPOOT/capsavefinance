<?php
namespace App\Inv\Repositories\Models\Lms;

use DB;
use Helpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Models\Master\Tds;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\Lms\InterestAccrualTemp;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use Illuminate\Support\Arr;

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
        'trans_id',
        'payment_id',
        'link_trans_id',
        'parent_trans_id',
        'invoice_disbursed_id',
        'trans_running_id',
        'user_id',
        'apportionment_type',
        'apportionment_id',
        'trans_date',
        'trans_type',
        'trans_mode',
        'amount',
        'outstanding',
        'actual_outstanding',
        'settled_outstanding',
        'entry_type',
        'from_date',
        'to_date',
        'due_date',
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

    public function linkTransactions(){
        return $this->belongsTo('App\Inv\Repositories\Models\Lms\Transactions','link_trans_id','trans_id');
    }

    public function payment(){
        return $this->belongsTo('App\Inv\Repositories\Models\Payment','payment_id','payment_id');
    } 

    public function invoiceDisbursed(){
        return $this->belongsTo('App\Inv\Repositories\Models\Lms\InvoiceDisbursed','invoice_disbursed_id','invoice_disbursed_id');
    }

    public function customerTransactionSOA(){
        return $this->hasOne('App\Inv\Repositories\Models\Lms\CustomerTransactionSOA','trans_id','trans_id');
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
    
    public function userInvLinkTrans(){
        return $this->hasOne('App\Inv\Repositories\Models\Lms\UserInvoiceTrans','trans_id','link_trans_id');
    }

    public function refundReqTrans(){
        return $this->hasOne('App\Inv\Repositories\Models\Lms\Refund\RefundReqTrans','trans_id','trans_id');
    }

    public function refundTrans(){
        return $this->hasOne('App\Inv\Repositories\Models\Lms\Refund\RefundReqTrans','refund_trans_id','trans_id');
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

    public function apportionment(){
        return $this->hasOne('App\Inv\Repositories\Models\Lms\Apportionment','apportionment_id','apportionment_id');
    }

    public function getInvoiceNoAttribute(){
          
        $invNo = '';

        if ($this->invoice_disbursed_id){
            $invNo = $this->invoiceDisbursed->invoice->billNo;
        }

        return $invNo;
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

    public function getFinalAmtAttribute()
    {
        $amount = 0;
        if($this->entry_type == '1' && is_null($this->payment_id) && is_null($this->link_trans_id) && is_null($this->parent_trans_id)){
            $cr = (float) self::where('parent_trans_id','=',$this->trans_id)
            ->where('entry_type','=','1')
            ->whereIn('trans_type',[config('lms.TRANS_TYPE.CANCEL')])
            ->sum('amount');
            
            $amount = round(($this->amount - $cr),2);
        }
        return $amount > 0 ? $amount : 0;
    }

    public function calculateSettledAmt($trans_id){
        $dr = self::where('parent_trans_id','=',$trans_id)
        ->where('entry_type','=','0')
        ->whereNotIn('trans_type',[config('lms.TRANS_TYPE.REFUND'),config('lms.TRANS_TYPE.ADJUSTMENT')])
        ->sum('amount');

        $cr = self::where('parent_trans_id','=',$trans_id)
        ->where('entry_type','=','1')
        ->sum('amount');

        return (float)$cr - (float)$dr;
    }

    public function calculateRevertedAmt($trans_id){
        return self::where('link_trans_id','=',$trans_id)
        ->whereIn('trans_type',[config('lms.TRANS_TYPE.REVERSE'),config('lms.TRANS_TYPE.CANCEL')])
        ->where(function($newQuery){
            $newQuery->where(function($q){
                $q->where('entry_type','=','0');
                $q->where('trans_type','=', config('lms.TRANS_TYPE.REVERSE'));
            });
            $newQuery->orWhere(function($q){
                $q->where('entry_type','=','1');
                $q->where('trans_type','=', config('lms.TRANS_TYPE.CANCEL'));
            });
        })
        ->sum('amount');
    }

    public function calculateOutstandings_old()
    {
        if($this->parent_trans_id){
            $parentTrans = $this->parentTransactions;
            if($parentTrans){
                if($parentTrans->entry_type == 0){
                    $settledAmt = self::calculateSettledAmt($this->parent_trans_id);
                    $outAmt = round(($parentTrans->amount - $settledAmt),2);
                    $outAmt = $outAmt > 0 ? $outAmt : 0;
                    self::where('trans_id', $this->parent_trans_id)->update(['outstanding' => $outAmt]);
                }
            }
        }else{
            if($this->entry_type == 0){
                $settledAmt = self::calculateSettledAmt($this->trans_id);
                $outAmt = round(($this->amount - $settledAmt),2);
                $outAmt = $outAmt > 0 ? $outAmt : 0;
                self::where('trans_id', $this->trans_id)->update(['outstanding' => $outAmt]);
            }
        }
        
        if($this->link_trans_id){
            if($this->link_trans_id == $this->parent_trans_id && $this->entry_type == 1){
                $revertedAmt = self::calculateRevertedAmt($this->trans_id);
                $revtAmt = round(($this->amount - $revertedAmt),2);
                $revtAmt = $revtAmt > 0 ? $revtAmt : 0;
                self::where('trans_id', $this->trans_id)->update(['settled_outstanding' => $revtAmt]);
            }

            $linkTrans = $this->linkTransactions;
            if($linkTrans){
                if($linkTrans->link_trans_id == $linkTrans->parent_trans_id &&  $linkTrans->entry_type == 1){
                    $revertedAmt = self::calculateRevertedAmt($this->link_trans_id);
                    $revtAmt = round(($linkTrans->amount - $revertedAmt),2);
                    $revtAmt = $revtAmt > 0 ? $revtAmt : 0;
                    self::where('trans_id', $this->link_trans_id)->update(['settled_outstanding' => $revtAmt]);
                }
            }
        }
    }

    public function calculateOutstandingsCreate(){
        $transType = $this->trans_type;
        $entryType = $this->entry_type;
        $linkLinkTrans = null;
        $linkTrans = null;
        $linkLinkLinkTrans = null;

        $trans = Transactions::find($this->trans_id);

        if($this->link_trans_id){
            $linkTrans = Transactions::find($trans->link_trans_id);
        }

        if($linkTrans && $linkTrans->link_trans_id){
            $linkLinkTrans = Transactions::find($linkTrans->link_trans_id);
        }

        if($linkLinkTrans && $linkLinkTrans->link_trans_id){
            $linkLinkLinkTrans = Transactions::find($linkLinkTrans->link_trans_id);
        }

        if($trans->entry_type == 0){
            $trans->outstanding = $this->amount;
            $trans->actual_outstanding = $this->amount;
            if($linkTrans){
                if($linkTrans->entry_type == 0){
                    $linkTrans->actual_outstanding += $this->amount;
                    $linkTrans->outstanding = ($linkTrans->actual_outstanding) > 0 ? ($linkTrans->actual_outstanding) : 0;
                    if($linkLinkTrans){
                        if($linkLinkTrans->entry_type == 0){
                            $linkLinkTrans->actual_outstanding += $this->amount;
                            $linkLinkTrans->outstanding = ($linkLinkTrans->actual_outstanding) > 0 ? ($linkLinkTrans->actual_outstanding) : 0;
                            if($linkLinkLinkTrans){
                                if($linkLinkLinkTrans->entry_type == 0){
                                    $linkLinkLinkTrans->actual_outstanding += $this->amount;
                                    $linkLinkLinkTrans->outstanding = ($linkLinkLinkTrans->actual_outstanding) > 0 ? ($linkLinkLinkTrans->actual_outstanding) : 0;        
                                }else{
                                    $linkLinkLinkTrans->settled_outstanding -= $this->amount;
                                }
                            }
                        }else{
                            $linkLinkTrans->settled_outstanding -= $this->amount;
                            if($linkLinkLinkTrans && !in_array($linkLinkTrans->trans_type, [32])){
                                if($linkLinkLinkTrans->entry_type == 0){
                                    $linkLinkLinkTrans->actual_outstanding += $this->amount;
                                    $linkLinkLinkTrans->outstanding = ($linkLinkLinkTrans->actual_outstanding) > 0 ? ($linkLinkLinkTrans->actual_outstanding) : 0;        
                                }else{
                                    $linkLinkLinkTrans->settled_outstanding -= $this->amount;
                                }
                            }
                        }
                    }
                }else{
                    $linkTrans->settled_outstanding -= $this->amount;
                    if($linkLinkTrans && !in_array($linkTrans->trans_type, [32])){
                        if($linkLinkTrans->entry_type == 0){
                            $linkLinkTrans->actual_outstanding += $this->amount;
                            $linkLinkTrans->outstanding = ($linkLinkTrans->actual_outstanding) > 0 ? ($linkLinkTrans->actual_outstanding) : 0;
                            if($linkLinkLinkTrans){
                                if($linkLinkLinkTrans->entry_type == 0){
                                    $linkLinkLinkTrans->actual_outstanding += $this->amount;
                                    $linkLinkLinkTrans->outstanding = ($linkLinkLinkTrans->actual_outstanding) > 0 ? ($linkLinkLinkTrans->actual_outstanding) : 0;        
                                }else{
                                    $linkLinkLinkTrans->settled_outstanding -= $this->amount;
                                }
                            }
                        }else{
                            $linkLinkTrans->settled_outstanding -= $this->amount;
                            if($linkLinkLinkTrans && !in_array($linkLinkTrans->trans_type, [32])){
                                if($linkLinkLinkTrans->entry_type == 0){
                                    $linkLinkLinkTrans->actual_outstanding += $this->amount;
                                    $linkLinkLinkTrans->outstanding = ($linkLinkLinkTrans->actual_outstanding) > 0 ? ($linkLinkLinkTrans->actual_outstanding) : 0;        
                                }else{
                                    $linkLinkLinkTrans->settled_outstanding -= $this->amount;
                                }
                            }
                        }
                    }
                }
            }

        }else{
            $trans->settled_outstanding = $this->amount;
            if($linkTrans && !in_array($trans->trans_type, [32])){
                if($linkTrans->entry_type == 0){
                    $linkTrans->actual_outstanding -= $this->amount;
                    $linkTrans->outstanding = ($linkTrans->actual_outstanding) > 0 ? ($linkTrans->actual_outstanding) : 0;
                    if($linkLinkTrans){
                        if($linkLinkTrans->entry_type == 0){
                            $linkLinkTrans->actual_outstanding += $this->amount;
                            $linkLinkTrans->outstanding = ($linkLinkTrans->actual_outstanding) > 0 ? ($linkLinkTrans->actual_outstanding) : 0;
                            if($linkLinkLinkTrans){
                                if($linkLinkLinkTrans->entry_type == 0){
                                    $linkLinkLinkTrans->actual_outstanding += $this->amount;
                                    $linkLinkLinkTrans->outstanding = ($linkLinkLinkTrans->actual_outstanding) > 0 ? ($linkLinkLinkTrans->actual_outstanding) : 0;        
                                }else{
                                    $linkLinkLinkTrans->settled_outstanding -= $this->amount;
                                }
                            }
                        }else{
                            $linkLinkTrans->settled_outstanding += $this->amount;
                            if($linkLinkLinkTrans && !in_array($linkLinkTrans->trans_type, [32])){
                                if($linkLinkLinkTrans->entry_type == 0){
                                    $linkLinkLinkTrans->actual_outstanding -= $this->amount;
                                    $linkLinkLinkTrans->outstanding = ($linkLinkLinkTrans->actual_outstanding) > 0 ? ($linkLinkLinkTrans->actual_outstanding) : 0;        
                                }else{
                                    $linkLinkLinkTrans->settled_outstanding += $this->amount;
                                }
                            }
                        }
                    }
                }else{
                    $linkTrans->settled_outstanding += $this->amount;
                    if($linkLinkTrans && !in_array($linkTrans->trans_type, [32])){
                        if($linkLinkTrans->entry_type == 0){
                            $linkLinkTrans->actual_outstanding += $this->amount;
                            $linkLinkTrans->outstanding = ($linkLinkTrans->actual_outstanding) > 0 ? ($linkLinkTrans->actual_outstanding) : 0;
                            if($linkLinkLinkTrans){
                                if($linkLinkLinkTrans->entry_type == 0){
                                    $linkLinkLinkTrans->actual_outstanding += $this->amount;
                                    $linkLinkLinkTrans->outstanding = ($linkLinkLinkTrans->actual_outstanding) > 0 ? ($linkLinkLinkTrans->actual_outstanding) : 0;        
                                }else{
                                    $linkLinkLinkTrans->settled_outstanding -= $this->amount;
                                }
                            }
                        }else{
                            $linkLinkTrans->settled_outstanding -= $this->amount;
                            if($linkLinkLinkTrans && !in_array($linkLinkTrans->trans_type, [32])){
                                if($linkLinkLinkTrans->entry_type == 0){
                                    $linkLinkLinkTrans->actual_outstanding += $this->amount;
                                    $linkLinkLinkTrans->outstanding = ($linkLinkLinkTrans->actual_outstanding) > 0 ? ($linkLinkLinkTrans->actual_outstanding) : 0;        
                                }else{
                                    $linkLinkLinkTrans->settled_outstanding -= $this->amount;
                                }
                            }
                        }
                    }
                }
            }
        }
        $trans->save();
        
        if($linkTrans){
            $linkTrans->save();
        }
        if($linkLinkTrans){
            $linkLinkTrans->save();
        }
        if($linkLinkLinkTrans){
            $linkLinkLinkTrans->save();
        }
    }

    public function calculateOutstandingsDelete(){
    
        $transType = $this->trans_type;
        $entryType = $this->entry_type;
        $parentTrans = null;
        $linkTrans = null;

        $trans = Transactions::find($this->trans_id);

        if($this->parent_trans_id){
            $parentTrans = Transactions::find($this->parent_trans_id);
        }

        if($this->link_trans_id){
            $linkTrans = Transactions::find($this->link_trans_id);
        }
        
        if($entryType == '0'){
            if(!is_null($this->parent_trans_id) && !is_null($this->link_trans_id) &&  $this->parent_trans_id <> $this->link_trans_id){   
                $linkTrans->settled_outstanding += $this->amount;
                if($transType == config('lms.TRANS_TYPE.REVERSE')){
                    $parentTrans->actual_outstanding -= $this->amount;
                    $parentTrans->outstanding = ($parentTrans->actual_outstanding) > 0 ? ($parentTrans->actual_outstanding) : 0;
                }
            }
        }
        
        elseif($entryType == '1'){
            if(!is_null($this->parent_trans_id) && !is_null($this->link_trans_id) &&  $this->parent_trans_id <> $this->link_trans_id){
                if($this->trans_type == '32'){
                    //$settledAmount = $linkTrans->settled_outstanding + $this->amount;
                    //self::where('trans_id', $linkTrans->trans_id)->update(['settled_outstanding' => $settledAmount]);
                    // if($linkTrans->trans_type == '7'){ 
                    //     $actualAmount = $parentTrans->actual_outstanding + $this->amount;
                    //     $amount = $actualAmount > 0 ? $actualAmount : 0;
                    //     self::where('trans_id', $parentTrans->trans_id)->update(['outstanding' => $amount,'actual_outstanding'=>$actualAmount]);
                    // }
                }
            }
            elseif(!is_null($this->parent_trans_id) && !is_null($this->link_trans_id) &&  $this->parent_trans_id == $this->link_trans_id){
                $parentTrans->actual_outstanding += $this->amount;
                $parentTrans->outstanding = ($parentTrans->actual_outstanding) > 0 ? ($parentTrans->actual_outstanding) : 0;
            }
        }
        if(!is_null($this->parent_trans_id) && !is_null($parentTrans)){
            $parentTrans->save();
        }
        if(!is_null($this->link_trans_id) && !is_null($linkTrans)){
            $linkTrans->save();
        }
    }
    
    public function scopeRevertedAmt($query){
        return $query->where('link_trans_id','=',$this->trans_id)
        ->whereIn('trans_type',[config('lms.TRANS_TYPE.REVERSE'),config('lms.TRANS_TYPE.CANCEL')])
        ->where(function($newQuery){
            $newQuery->where(function($q){
                $q->where('entry_type','=','0');
                $q->where('trans_type','=', config('lms.TRANS_TYPE.REVERSE'));
            });
            $newQuery->orWhere(function($q){
                $q->where('entry_type','=','1');
                $q->where('trans_type','=', config('lms.TRANS_TYPE.CANCEL'));
            });
        })
        ->sum('amount');
    }

    public function getIsTransactionFlagAttribute(){
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

    public static function maxDpdTransaction($user_id){
        ini_set("memory_limit", "-1");
        return  Transactions::where('user_id',$user_id)
        ->where('entry_type',0)
        ->whereNull('link_trans_id')
        ->whereNull('parent_trans_id')
        ->whereIn('trans_type',[config('lms.TRANS_TYPE.PAYMENT_DISBURSED'),config('lms.TRANS_TYPE.INTEREST')])
        ->get()
        ->max('dpd');
    }
    // public function getSettledOutstandingAttribute(){
    //     return round(($this->amount - self::revertedAmt()),2);
    // }

    public function getDpdAttribute(){
        $to = Carbon::parse(Helpers::getSysStartDate())->format('Y-m-d');
        $number_days = 0;
        if($this->entry_type == 0){
            $from = Carbon::parse($this->paymentduedate)->format('Y-m-d');
            if($this->outstanding > 0){
                switch ($this->trans_type) {
                    case config('lms.TRANS_TYPE.PAYMENT_DISBURSED'):
                        $graceEnd = Carbon::parse($from)->addDays($this->invoiceDisbursed->grace_period ?? 0)->format('Y-m-d');
                        if(strtotime($to) >= strtotime($graceEnd)){
                            $number_days = (strtotime($to) - strtotime($graceEnd)) / (60 * 60 * 24);
                        }
                    break;
                    case config('lms.TRANS_TYPE.INTEREST'):
                        if(strtotime($to) > strtotime($from)){
                            $number_days = (strtotime($to) - strtotime($from)) / (60 * 60 * 24);
                        }
                    break;
                    case config('lms.TRANS_TYPE.INTEREST_OVERDUE'):
                        if(strtotime($to) > strtotime($from)){
                            $number_days = (strtotime($to) - strtotime($from)) / (60 * 60 * 24);
                        }
                    break;
                }
            }
        }
        return $number_days;
    }

    public function getWaiveOffAmount(){
        return self::where(['parent_trans_id' => $this->trans_id, 'trans_type' => config('lms.TRANS_TYPE.WAVED_OFF')])->sum('settled_outstanding');
    }

    public function getCancelledAmount(){
        return self::where(['link_trans_id' => $this->trans_id, 'trans_type' => config('lms.TRANS_TYPE.CANCEL')])->sum('settled_outstanding');
    }

    public function getRefundableAmtAttribute(){
        return self::where('link_trans_id','=',$this->trans_id)
        ->where('entry_type','=','0')
        ->whereIn('trans_type',[config('lms.TRANS_TYPE.REVERSE'),config('lms.TRANS_TYPE.REFUND'),config('lms.TRANS_TYPE.ADJUSTMENT')]) 
        ->sum('outstanding');
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
        $name = ''; 

        if($this->trans_type == config('lms.TRANS_TYPE.REPAYMENT')) 
        return $this->payment->paymentname??'';

        if(in_array($this->trans_type,[config('lms.TRANS_TYPE.WRITE_OFF'),config('lms.TRANS_TYPE.WAVED_OFF'),config('lms.TRANS_TYPE.TDS'),config('lms.TRANS_TYPE.REVERSE'),config('lms.TRANS_TYPE.REFUND'),config('lms.TRANS_TYPE.CANCEL'),config('lms.TRANS_TYPE.ADJUSTMENT')])){
            if($this->link_trans_id){
                $linkTrans = $this->linkTransactions;
                if($linkTrans && $linkTrans->trans_type == config('lms.TRANS_TYPE.REFUND') && $linkTrans->entry_type == '1'){
                    $name .= ' '.$linkTrans->linkTransactions->customerTransactionSOA->trans_name;
                }elseif($linkTrans){
                    $name .= ' '.$linkTrans->customerTransactionSOA->trans_name;
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
            return $this->invoiceDisbursed->disbursal->disbursal_batch->batch_id ?? null;
        }
    }

    public function getNarrationAttribute(){
        $data = '';
        $pTrans = $this->parentTransactions;
        if(in_array($this->trans_type,[config('lms.TRANS_TYPE.REPAYMENT')])){
            if($this->BatchNo)
            $data .= $this->BatchNo.' ';
            if($this->payment->transactionno ?? NULL)
            $data .= $this->payment->paymentmode.': '.$this->payment->transactionno.' ' ;
            $data .= ' Payment Allocated as Normal: INR '. number_format($this->payment->amount ?? 0,2) . ' ';
        }
        elseif(in_array($this->trans_type,[config('lms.TRANS_TYPE.FAILED')])){
            $data .= ' Payment Failed as Normal: INR '. number_format($this->payment->amount ?? 0,2) . ' ';
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
        switch ($this->apportionment->apportionment_type ?? null) {
            case '1':
                    if($this->trans_type == config('lms.TRANS_TYPE.REPAYMENT'))
                    $color = '#f3c714';
                    elseif(!in_array($this->trans_type, [config('lms.TRANS_TYPE.TDS')]))
                    $color = '#ffe787';
                break;
            case '2':
                    if($this->linkTransactions->trans_type == config('lms.TRANS_TYPE.REPAYMENT'))
                    $color = '#ff6767';
                    elseif(!in_array($this->linkTransactions->trans_type, [config('lms.TRANS_TYPE.TDS')]))
                    $color = '#ffcccc';
                break;
            
            default:
                    $color = '';
                break;
        }
        return $color;
    }
    
    public  function getBalanceAttribute()
    {
        return self::get_balance($this->user_id.Carbon::parse($this->created_at)->format('ymd').(1000000000+$this->trans_id), $this->user_id);
    }

    public  function getPaymentDueDateAttribute(){  
        if($this->due_date){
            return carbon::parse($this->due_date)->format('Y-m-d');
        } 
        if(in_array($this->trans_type,[
            config('lms.TRANS_TYPE.PAYMENT_DISBURSED'),
            config('lms.TRANS_TYPE.MARGIN'),
            config('lms.TRANS_TYPE.INTEREST'),
            config('lms.TRANS_TYPE.INTEREST_OVERDUE')]) && $this->entry_type == 0 && is_null($this->link_trans_id) && is_null($this->parent_trans_id)){
        
            $paymentFrequency = $this->invoiceDisbursed->invoice->program_offer->payment_frequency;
            $intBornBy = $this->invoiceDisbursed->invoice->program_offer->interest_borne_by;
            $prgmType = $this->invoiceDisbursed->invoice->program->prgm_type;

            if(in_array($this->trans_type,[config('lms.TRANS_TYPE.PAYMENT_DISBURSED'),config('lms.TRANS_TYPE.MARGIN')])){
                return Carbon::parse($this->invoiceDisbursed->payment_due_date)->format('Y-m-d');
            }elseif(in_array($this->trans_type,[config('lms.TRANS_TYPE.INTEREST')]) && is_null($this->trans_running_id)){
                if($paymentFrequency == 1 && $intBornBy == 1){
                    return Carbon::parse($this->invoiceDisbursed->int_accrual_start_dt)->format('Y-m-d');
                }
            }
            return Carbon::parse($this->trans_date)->format('Y-m-d');
        }else{
            return Carbon::parse($this->trans_date)->format('Y-m-d');
        }
    }

    public function getTDSRateAttribute(){
        if($this->transType->chrg_master_id || in_array($this->trans_type,[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')])){
            return Tds::getActiveTdsBaseRate($this->trans_date);
        }else{
            return null;
        }
    }

    public function getTDSAmountAttribute(){
        $amount = $this->amount;
        $baseAmt = 0;
        $tdsAmt = 0;
        $gstAmt = 0;
        $gst_per = 0;
        if($this->transType->chrg_master_id || in_array($this->trans_type,[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')]) ){
            $tdsRate =  Tds::getActiveTdsBaseRate($this->trans_date);
            $amount -= $this->getCancelledAmount();
            
            if($this->transType->chrg_master_id && $this->transType->charge && $this->transType->charge->gst_percentage){
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
        ->where('settled_outstanding','>',0)
        ->get();   
        foreach($tds as $tdsTrans){
            $tdsAmt -= round($tdsTrans->settled_outstanding,2);
        }

        if($tdsAmt < 0 ){
            $tdsAmt = 0;
        }
        return round($tdsAmt,2);
    }

    public static function getUnsettledTrans($userId, $where = []){
        $query = self::whereNull('parent_trans_id')
                ->whereNull('payment_id')
                ->where('user_id',$userId)
                ->where('outstanding', '>', 0);

        if(!empty($where['trans_type_not_in'])){
            $query = $query->whereNotIn('trans_type',$where['trans_type_not_in']); 
        }
        if(!empty($where['trans_type_in'])){
            $query = $query->whereIn('trans_type',$where['trans_type_in']); 
        }
        if(!empty($where['trans_date'])){
            $query = $query->where('trans_date', '<=', $where['trans_date']);
        }
        return $query->get();
    }

    public static function getUserOutstanding($userId){
        $trans = self::getUnsettledTrans($userId);
    }

    public static function getSettledTrans($userId){
        return self::where('entry_type','1')
                //->whereNotNull('parent_trans_id')
                ->whereNotIn('trans_type',[config('lms.TRANS_TYPE.REPAYMENT'),config('lms.TRANS_TYPE.CANCEL'),config('lms.TRANS_TYPE.REFUND'),config('lms.TRANS_TYPE.REVERSE'),config('lms.TRANS_TYPE.NON_FACTORED_AMT')])
                ->where('user_id','=',$userId)
                ->where('settled_outstanding','>',0)
                ->get();
    }

    public static function getRefundTrans($userId){
        return self::where('entry_type','1')
        ->whereIn('trans_type',[config('lms.TRANS_TYPE.REFUND')])
        ->where('settled_outstanding','>',0)
        ->where('user_id','=',$userId)
        ->whereDoesntHave('refundReqTrans')
        ->get();
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

        if(isset($transactions['trans_type'])){
            $transType = $transactions['trans_type'];
            $chrg_id = TransType::where('id',$transType)->value('chrg_master_id');
            if($chrg_id > 0){
                $transactions['due_date'] = $transactions['trans_date'];
            }else{
            }
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
       
        $query =  self::whereNull('parent_trans_id')->whereNull('payment_id')->where('entry_type',0);

        if(isset($data['due_date'])){
            $query->where(function($q) use($data) {
                $q->orWhere(function($q2) use($data) {
                    $q2->whereIn('trans_type',[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')])->whereDate('due_date','<=',$data['due_date']);
                })
                ->orWhere(function($q2) use($data) {
                    $q2->whereIn('trans_type',[config('lms.TRANS_TYPE.PAYMENT_DISBURSED'),config('lms.TRANS_TYPE.MARGIN')])->whereDate('trans_date','<=',$data['due_date']);
                })
                ->orWhere(function($q2) use($data) {
                    $q2->whereHas('transType', function($q3){
                        $q3->where('chrg_master_id','>',0);
                    })->whereDate('due_date','<=',$data['due_date']);
                });
            });
        }

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
        
        $query = $query->orderByRaw("FIELD(trans_type, '".config('lms.TRANS_TYPE.INTEREST')."', '".config('lms.TRANS_TYPE.PAYMENT_DISBURSED')."', '".config('lms.TRANS_TYPE.INTEREST_OVERDUE')."', '".config('lms.TRANS_TYPE.MARGIN')."' ), trans_id");
 
        return $query->where('outstanding','>', 0)->get();
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

        return $query->where('outstanding','>', 0)
        ->get();
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
                $totalInterestAmt = 0;
                $accruedInterest = $invoice->accruedInterest();
                if($payment_date){
                    $accruedInterest = $accruedInterest->whereDate('interest_date','<',$payment_date);
                    $totalInterestAmt = round($accruedInterest->sum('accrued_interest'),2);
                } 

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
        return self::with('transType')
        ->where(function ($query) {
            $query->whereHas('transType', function($q) { 
                $q->where('entry_type', '=', '0')->where('is_invoice_generated', '=', '1')->where(function ($qry) {
                        $qry->Where('chrg_master_id','!=','0');
                    });
            })
            ->orWhere(function ($q) {
                $q->whereIn('trans_type', [config('lms.TRANS_TYPE.REFUND')])->whereNotNull('parent_trans_id')->where('entry_type', '=', '1');
            })
            ->orWhere(function ($q) {
                $q->whereIn('trans_type', [config('lms.TRANS_TYPE.WAVED_OFF'), config('lms.TRANS_TYPE.WRITE_OFF'),config('lms.TRANS_TYPE.TDS'), config('lms.TRANS_TYPE.CANCEL')])->where('entry_type', '=', '1');
            })
            ->orWhere(function ($q) {
                $q->WhereIn('trans_type', [config('lms.TRANS_TYPE.INTEREST'), config('lms.TRANS_TYPE.INTEREST_OVERDUE')])->where('entry_type', '=', '0');
            })
            ->orWhere(function ($q) {
                $q->WhereIn('trans_type', [config('lms.TRANS_TYPE.REVERSE')])->whereNull('payment_id');
            });
        })->where($where)->orderBy('trans_id')->get();
    }

    public static function getDisbursalTxnTally(array $where = []){
        return self::whereIn('trans_type', [config('lms.TRANS_TYPE.PAYMENT_DISBURSED')])
            ->where('entry_type', '=', 0)
            ->where($where)
            ->orderBy('trans_id')
            ->get();
    }

    public static function getRefundTxnTally(array $where = []){
        return self::whereIn('trans_type', [config('lms.TRANS_TYPE.REFUND')])
            ->where('entry_type', '=', 0)
            ->where($where)
            ->orderBy('trans_id')
            ->get();
    }

    public static function getMaxDpdTransaction($userId, $transType){
        $transactions =  self::whereNull('parent_trans_id')
        ->whereNull('payment_id')
        ->where('user_id','=',$userId)
        ->where('trans_type','=',$transType)        
        ->where('outstanding', '>', 0)
        ->orderByRaw("FIELD(trans_type, '".config('lms.TRANS_TYPE.INTEREST')."', '".config('lms.TRANS_TYPE.PAYMENT_DISBURSED')."', '".config('lms.TRANS_TYPE.INTEREST_OVERDUE')."', '".config('lms.TRANS_TYPE.MARGIN')."' )")
        ->get();
        $maxDPD = $transactions->max('dpd');
        return $transactions->where('dpd','=',$maxDPD)->first();
    }

    public static function getMaxDpdInvoiceTransaction($invDesbId, $transType){
        $transactions =  self::whereNull('parent_trans_id')
        ->whereNull('payment_id')
        ->where('invoice_disbursed_id','=',$invDesbId)
        ->where('trans_type','=',$transType)        
        ->where('outstanding', '>', 0)
        ->get();
        return $maxDPD = $transactions->max('dpd');
    }

    /*** save repayment transaction details for invoice  **/
    public static function saveRepaymentTrans($transactions)
    {
        return self::saveTransaction($transactions);
        // $transactions['sys_updated_at'] = Helpers::getSysStartDate();
        // $transactions['sys_created_at'] = Helpers::getSysStartDate();
        // return self::create($transactions);
    }
    
    /*** save repayment transaction details for invoice  **/
    public static function saveCharge($transactions)
    {
        return self::saveTransaction($transactions);
        // $transactions['sys_updated_at'] = Helpers::getSysStartDate();
        // $transactions['sys_created_at'] = Helpers::getSysStartDate();
        // return self::create($transactions);
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
                    ->orderBy('trans_date', 'asc')
                    ->orderBy('trans_id', 'asc');
    }

    public static function getSoaList(){

        return self::select('transactions.*')
                    ->where('soa_flag','=',1)
                    ->orderBy('user_id', 'asc')
                    ->orderBy('trans_date', 'asc')
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
                    ->orderBy('trans_date', 'asc')
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
        ->get();
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
       return $this->hasOne('App\Inv\Repositories\Models\Lms\UserInvoiceRelation', 'user_id', 'user_id');//->where('is_active', 1);
    }

    public static function getUserInvoiceTxns($userId, $invoiceType, $trans_ids, $is_force = false){
       $sql = self::with('transType','invoiceDisbursed:invoice_disbursed_id,invoice_id','invoiceDisbursed.invoice:invoice_id,program_id','invoiceDisbursed.invoice.program:prgm_id,interest_borne_by,overdue_interest_borne_by','ChargesTransactions:trans_id,prgm_id','ChargesTransactions.chargePrgm:prgm_id,interest_borne_by')->whereNull('payment_id')->where(['user_id' => $userId, 'entry_type' => 0]);
       if (!empty($trans_ids)) {
        if ($is_force) {
            $sql->where('is_invoice_generated', '=', 0);
        }
        $sql->whereIn('trans_id', $trans_ids);
       }else{
          $sql->where('is_invoice_generated', '=', 0);
       }

        if($invoiceType == 'CA') {
            if (!$is_force && empty($trans_ids)) {
                $sql->whereHas('ChargesTransactions', function($newQuery) {
                    $newQuery->where('level_charges',1)->doesntHave('deleteLogs');
                });
            }
        }elseif($invoiceType == 'CC') {
            if (!$is_force && empty($trans_ids)) {
                $sql->whereHas('ChargesTransactions', function($newQuery) {
                    $newQuery->where('level_charges',2)->doesntHave('deleteLogs');
                });
            }
        }
        $sql->where(function($query) use ($invoiceType) {
            if ($invoiceType == 'IC') {
                $query->whereHas('transType', function($newQuery) {
                    $newQuery->whereIn('id',[config('lms.TRANS_TYPE.INTEREST')]);
                })
                ->whereHas('invoiceDisbursed.invoice.program', function($newQuery) {
                    $newQuery->where('interest_borne_by',2);
                })
                ->orWhere(function($newQuery) use ($invoiceType) {
                    $newQuery->whereHas('transType', function($innerQuery) {
                        $innerQuery->whereIn('id',[config('lms.TRANS_TYPE.INTEREST_OVERDUE')]);
                    })
                    ->whereHas('invoiceDisbursed.invoice.program', function($innerQuery) {
                        $innerQuery->where('overdue_interest_borne_by',2);
                    });
                });
            } elseif ($invoiceType == 'IA') {
                $query->whereHas('transType', function($newQuery) {
                    $newQuery->whereIn('id',[config('lms.TRANS_TYPE.INTEREST')]);
                })
                ->whereHas('invoiceDisbursed.invoice.program', function($newQuery) {
                    $newQuery->where('interest_borne_by',1);
                })
                ->orWhere(function($newQuery) use ($invoiceType) {
                    $newQuery->whereHas('transType', function($innerQuery) {
                        $innerQuery->whereIn('id',[config('lms.TRANS_TYPE.INTEREST_OVERDUE')]);
                    })
                    ->whereHas('invoiceDisbursed.invoice.program', function($innerQuery) {
                        $innerQuery->where('overdue_interest_borne_by',1);
                    });
                });
            }
        });
        
       return $sql->whereHas('transType', function($query) use ($invoiceType) { 
            if($invoiceType == 'IC' || $invoiceType == 'IA') {
                $query->whereIn('id',[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')]);
             }elseif($invoiceType == 'CC' || $invoiceType == 'CA') {
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
        return self::whereBetween('sys_created_at', [$transStartDate, $transEndDate])
        ->whereIn('trans_type',[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')])
        ->where('trans_mode','2')
        ->whereNotNull('trans_running_id')
        ->where('outstanding','>',0)
        ->get();

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
        ->where('outstanding', '>', 0)
        ->get();

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
    
    public function getFromIntDateAttribute($paymentFrequency = null, $disbursedDate = null, $paymentDate = null){
        if($this->from_date){
            return carbon::parse($this->from_date)->format('Y-m-d');
        }
        $fromDate = null;
        if(in_array($this->trans_type,[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')])){
            
            $paymentFrequency = $paymentFrequency ?? $this->invoiceDisbursed->invoice->program_offer->payment_frequency;
            $disbursedDate = $disbursedDate ?? date('Y-m-d', strtotime($this->invoiceDisbursed->int_accrual_start_dt));
            $paymentDate = $paymentDate ?? date('Y-m-d', strtotime($this->invoiceDisbursed->payment_due_date));
            $transDate = date('Y-m-d', strtotime($this->trans_date));
            
            $lastTrans = self::where('invoice_disbursed_id',$this->invoice_disbursed_id)
            ->whereDate('trans_date','<=',$this->trans_date)
            ->where('trans_id','<',$this->trans_id)
            ->where('trans_type',$this->trans_type)
            ->whereNull('link_trans_id')
            ->whereNull('parent_trans_id')
            ->where('entry_type','0')
            ->orderBy('trans_date','DESC')
            ->limit(1)->get('trans_date')->first();

            $lastTransToDate = ($lastTrans)?date('Y-m-d', strtotime($lastTrans->trans_date)):null;

            if(!$lastTransToDate){
                if($paymentFrequency == 1){
                    if($transDate == $disbursedDate){
                        $fromDate = date('Y-m-d', strtotime($disbursedDate));
                    }
                    elseif($transDate == $paymentDate){
                        $fromDate = date('Y-m-d', strtotime($paymentDate . "- 1 days"));
                    }elseif($this->trans_type == config('lms.TRANS_TYPE.INTEREST')){
                        $fromDate = date('Y-m-d', strtotime($this->trans_date));
                    }elseif($this->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')){
                        $fromDate = date('Y-m-d', strtotime($paymentDate));
                    }
                }else{
                    if($this->trans_type == config('lms.TRANS_TYPE.INTEREST')){
                        $fromDate = date('Y-m-d', strtotime($disbursedDate));
                    }elseif($this->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')){
                        $fromDate = date('Y-m-d', strtotime($paymentDate));
                    }
                }
            }else{
                if($paymentFrequency == 1){
                    if($lastTransToDate == $disbursedDate){
                        $fromDate = date('Y-m-d', strtotime($disbursedDate));
                    }
                    elseif($lastTransToDate == $paymentDate && $this->trans_type == config('lms.TRANS_TYPE.INTEREST')){
                        $fromDate = date('Y-m-d', strtotime($paymentDate . "- 1 days"));
                    }elseif($this->trans_type == config('lms.TRANS_TYPE.INTEREST')){
                        $fromDate = $lastTransToDate;
                    }elseif($this->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')){
                        $fromDate = date('Y-m-d', strtotime($lastTransToDate . "+ 1 days"));
                    }
                }else{
                    $fromDate = date('Y-m-d', strtotime($lastTransToDate . "+ 1 days"));
                }
            }

        }
        return $fromDate;
    }

    public function getToIntDateAttribute($paymentFrequency = null, $disbursedDate = null, $paymentDate = null){
        if($this->to_date){
            return carbon::parse($this->to_date)->format('Y-m-d');
        }
        
        $toDate = null;
        if(in_array($this->trans_type,[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')])){
            $paymentFrequency = $paymentFrequency ?? $this->invoiceDisbursed->invoice->program_offer->payment_frequency;
            $disbursedDate = $disbursedDate ?? date('Y-m-d', strtotime($this->invoiceDisbursed->int_accrual_start_dt));
            $paymentDate = $paymentDate ?? date('Y-m-d', strtotime($this->invoiceDisbursed->payment_due_date));
            $transDate = date('Y-m-d', strtotime($this->trans_date));
            if($paymentFrequency == 1){
               if($transDate == $disbursedDate){
                    $toDate = date('Y-m-d', strtotime($paymentDate . "- 1 days"));
                }else{
                    $toDate = date('Y-m-d', strtotime($transDate));
                }
            }else{
                $toDate = date('Y-m-d', strtotime($transDate));
            }
        }
        return $toDate;
    }

    public function getActInterestAttribute(){
        $from = self::getFromIntDateAttribute();
        $to = self::getToIntDateAttribute();
        $transType = $this->trans_type;
        $invoice_disbursed_id = $this->invoice_disbursed_id;
        if($from && $to && $invoice_disbursed_id && in_array($transType,[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')])){
            $actualAmount = InterestAccrual::whereDate('interest_date', '>=',$from)
            ->whereDate('interest_date','<=',$to)
            ->where(function($query) use($transType){
                if($transType == 9){
                    $query->whereNotNull('interest_rate'); 
                }
                elseif($transType == 33){
                    $query->whereNotNull('overdue_interest_rate');
                }
            })
            ->where('invoice_disbursed_id', $invoice_disbursed_id)
            ->sum('accrued_interest');
            return round($actualAmount,2);
        }
    }

    public function getTempInterestAttribute(){
        $amount = null;
        $from = self::getFromIntDateAttribute();
        $to = self::getToIntDateAttribute();
        $outstanding = $this->outstanding;
        $invoice_disbursed_id = $this->invoice_disbursed_id;
        $transType = $this->trans_type;
        if($from && $to && $invoice_disbursed_id && in_array($transType,[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')])){
            $amount = InterestAccrualTemp::whereDate('interest_date','>=',$from)
            ->whereDate('interest_date','<=',$to)
            // ->where(function ($query) use ($transType){
            //     if($transType == config('lms.TRANS_TYPE.INTEREST')){
            //         $query->whereNotNull('interest_rate');
            //     }elseif($transType == config('lms.TRANS_TYPE.INTEREST_OVERDUE')){
            //         $query->whereNotNull('overdue_interest_rate');
            //     }
            // })
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
            ->get()
            ->filter(function($item) use($whereCondition) {
                $result = false;
                if($whereCondition){
                    if($whereCondition['from_date'] && $whereCondition['to_date']){
                        $result = (strtotime($item->fromIntDate) >= strtotime($whereCondition['from_date']) && strtotime($item->fromIntDate) <= strtotime($whereCondition['to_date']));
                    }else{
                        $result = true;    
                    }
                }else{
                    $result = true;
                }
                return $result; 
            });

            foreach($unIntTrans as $uITrans){
                $data[strtotime($uITrans->fromIntDate).'-'.$uITrans->trans_id] = 
                [
                    'loan' => config('common.idprefix.APP').$uITrans->invoiceDisbursed->invoice->app_id,
                    'cust_id' => $uITrans->invoiceDisbursed->invoice->lms_user->customer_id,
                    'client_name' => $uITrans->user->biz->biz_entity_name,
                    'disbursed_amt' => $dTrans->amount,
                    'from_date' => $uITrans->fromIntDate,
                    'to_date' => $uITrans->toIntDate,
                    'days' => abs(round((strtotime($uITrans->toIntDate) - strtotime($uITrans->fromIntDate)) / 86400))+1,
                    'int_rate' => $uITrans->invoiceDisbursed->interest_rate,
                    'int_amt' => $uITrans->amount,
                    'collection_date' => null,
                    'tds_rate' => null,
                    'tds_amt' => 0,
                    'net_int' => 0,
                    'tally_batch' => ''
                ];
                $data[strtotime($uITrans->fromIntDate).'-'.$uITrans->trans_id]['collection_date'] = self::where('trans_type', config('lms.TRANS_TYPE.INTEREST'))
                ->where('invoice_disbursed_id', $uITrans->invoice_disbursed_id)
                ->where('parent_trans_id', $uITrans->trans_id)
                ->where('entry_type','1')
                ->max('trans_date');

                $data[strtotime($uITrans->fromIntDate).'-'.$uITrans->trans_id]['tds_amt'] = self::where('trans_type', config('lms.TRANS_TYPE.TDS'))
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
                
                $data[strtotime($uITrans->fromIntDate).'-'.$uITrans->trans_id]['tds_rate'] = implode(',', $tdsRates);

                $data[strtotime($uITrans->fromIntDate).'-'.$uITrans->trans_id]['net_int'] = $data[strtotime($uITrans->fromIntDate).'-'.$uITrans->trans_id]['int_amt'] - $data[strtotime($uITrans->fromIntDate).'-'.$uITrans->trans_id]['tds_amt'];

                $tallyEntries =  $uITrans->tallyEntry;

                if($tallyEntries){
                    $tallyEntries = $tallyEntries->first();
                    $data[strtotime($uITrans->fromIntDate).'-'.$uITrans->trans_id]['tally_batch'] = $tallyEntries->batch_no;
                }
            }
        }
        krsort($data);
        return $data;
    }

    public static function getchargeBreakupReport($whereCondition=[], $whereRawCondition = NULL){
        $data = [];

        $chargTrans = self::whereHas('transType', function($query){
            $query->where('chrg_master_id','>','0');
        })
        ->whereNull('parent_trans_id')
        ->where('entry_type','0')
        ->orderBy('trans_date', 'desc');
        
        if (!empty($whereRawCondition)) {
            $chargTrans = $chargTrans->whereRaw($whereRawCondition);
        }

        $chargTrans = $chargTrans->get();
        foreach($chargTrans as $cTrans){
            $data[$cTrans->trans_id] = 
            [
                'loan' => '',
                'cust_id' => $cTrans->user->lms_user->customer_id,
                'client_name' =>$cTrans->user->biz->biz_entity_name,
                'chrg_name' => $cTrans->transName,
                'trans_date' => $cTrans->trans_date,
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
                $data[$cTrans->trans_id]['chrg_amt'] = $cTrans->userInvTrans->base_amount;
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

        $chargTrans = self::
        where(function ($query) {
            $query->whereHas('transType', function($q) { 
                $q->where('id', '=', config('lms.TRANS_TYPE.INTEREST'))->orWhere('chrg_master_id','!=','0');
            });
        })
        ->whereNull('parent_trans_id')
        ->where('entry_type','0');

        if (!empty($whereRawCondition)) {
            $chargTrans = $chargTrans->whereRaw($whereRawCondition);
        }
        $chargTrans = $chargTrans->get();

        
        foreach($chargTrans as $cTrans){

            $tdsTrans = self::where('trans_type', config('lms.TRANS_TYPE.TDS'))
            ->whereNotNull('payment_id')
            ->where('parent_trans_id', $cTrans->trans_id)
            ->where('entry_type','1')
            ->get();

            foreach($tdsTrans as $tds){

                $data[strtotime($tds->trans_date).'-'.$tds->trans_id] = 
                [
                    'loan' => '',
                    'cust_id' => $tds->user->lms_user->customer_id,
                    'client_name' => $tds->user->biz->biz_entity_name,
                    'trans_date' => $tds->trans_date,
                    'int_amt' => $cTrans->amount,
                    'deduction_date' => $cTrans->trans_date,
                    'tds_amt' => $tds->amount,
                    'tds_certificate' => $tds->payment->tds_certificate_no,
                    'tally_batch' => ''
                ];
                
                if(in_array($cTrans->trans_type, [config('lms.TRANS_TYPE.INTEREST_OVERDUE'),config('lms.TRANS_TYPE.INTEREST')])){
                    $data[strtotime($tds->trans_date).'-'.$tds->trans_id]['loan'] = config('common.idprefix.APP').$cTrans->invoiceDisbursed->invoice->app_id;
                }else{
                    $charge = $cTrans->chargesTransactions;
                    if($charge){
                        $data[strtotime($tds->trans_date).'-'.$tds->trans_id]['loan'] = $charge->app_id?config('common.idprefix.APP').$charge->app_id:'';
                    }
                }
                $tallyEntries =  $tds->tallyEntry;
                if($tallyEntries){
                    $tallyEntries = $tallyEntries->first();
                    $data[strtotime($tds->trans_date).'-'.$tds->trans_id]['tally_batch'] = $tallyEntries->batch_no;
                }
            }
        }
        krsort($data);
        return $data;
    }
    
    public function getInterestForDisbursal(array $where = []) {
      return $this->belongsTo('App\Inv\Repositories\Models\Lms\Transactions', 'invoice_disbursed_id', 'invoice_disbursed_id')->where($where)->first();
    }

    public function nachTansReq() {
      return $this->hasOne('App\Inv\Repositories\Models\Lms\NachTransReq', 'trans_id', 'trans_id')->whereIn('status', [1,2]);
    }

    public static function getNACHUnsettledTrans($userId, $where = []) {
        $query = self::whereNull('parent_trans_id')
                ->whereNull('payment_id')
                ->where('user_id',$userId)
                ->doesntHave('nachTansReq');
        if(!empty($where['trans_type_not_in'])){
            $query = $query->whereNotIn('trans_type',$where['trans_type_not_in']); 
        }
        if(!empty($where['trans_type_in'])){
            $query = $query->whereIn('trans_type',$where['trans_type_in']); 
        }

        return $query
        ->where('outstanding','>',0)
        ->get()
            ->filter(function($item) {
                return ($item->paymentDueDate < date('Y-m-d'));
            });
    }

    public static function getUnsettledSettledTDSTrans($userId, $where = []){
        $query = self::whereNull('parent_trans_id')
                ->whereNull('payment_id')
                ->where('user_id',$userId);
        if(!empty($where['trans_type_not_in'])){
            $query = $query->whereNotIn('trans_type',$where['trans_type_not_in']);
        }
        if(!empty($where['trans_type_in'])){
            $query = $query->whereIn('trans_type', $where['trans_type_in']);
        }
        if(!empty($where['due_date'])){
            $query = $query->whereDate('due_date', '<=', $where['due_date']);
        }
        $query->where(function ($query1) {
            $query1->whereIn('trans_type', [config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')])
            ->orWhere(function ($query3) {
                $query3->whereHas('transType', function($query4) {
                    $query4->where('chrg_master_id' , '>', 0);
                });
            });
        });
        return $query->get()->filter(function($item) {
                    return ($item->TDSAmount > 0 );
                });
    }
    
    public function tdsProcessingFee() {
        $transId = self::where('trans_type',62)->where('entry_type',0)->pluck('trans_id');
        $getData = self::whereIn('link_trans_id',$transId)->where('trans_type',7)->where('entry_type',1)->sum('amount');
        return $getData;
    }


    public function deleteAllChild(){
       /* self::where('parent_trans_id',$this->transId)
        ->orWhere('link_trans_id',$this->transId)
        ->orderBy('trans_id','DESC')
        ->get()
        ->each
        ->delete();*/
    }

    public static function processChrgTransDeletion($trans)
    {
        $transIds = [];
        foreach($trans as $newTrans) {
            $newChrgTransArray = [
                'user_id' => $newTrans->user_id,
                'invoice_disbursed_id' => $newTrans->invoice_disbursed_id,
                'amount' => $newTrans->amount,
                'gst' => $newTrans->gst,
                'trans_mode' => 2,
                'parent_trans_id' => $newTrans->trans_id,
                'link_trans_id' => $newTrans->trans_id,
                'trans_date' => \Helpers::getSysStartDate(),
                'trans_type' => config('lms.TRANS_TYPE.CANCEL'),
                'entry_type' => 1,
            ];

            $newChrgTrans = self::saveTransaction($newChrgTransArray);
            $transIds[] = $newChrgTrans->trans_id;
        }

        return $transIds;
    }

    public function getCapsaveInvoiceNoAttribute()
    {
        $invNo = '';

        if ($this->userInvTrans) {
            $userInvoice = $this->userInvTrans->getUserInvoice;
            if($userInvoice->invoice_cat == 3){
                $invNo = $this->userInvLinkTrans->getUserInvoice->invoice_no;
            }else{
                $invNo = $userInvoice->invoice_no;
            }
        } elseif($this->userInvParentTrans) {
            $invNo = $this->userInvParentTrans->getUserInvoice->invoice_no;
        }

        return $invNo;
    }
}
