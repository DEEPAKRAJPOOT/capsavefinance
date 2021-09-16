<?php

namespace App\Inv\Repositories\Models\Lms;

use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Factory\Models\BaseModel;

class InvoiceDisbursedDetail extends BaseModel
{
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'invoice_disbursed_details';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'invoice_disbursed_detail_id';

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
        'invoice_id',
        'invoice_disbursed_id',
        'request_amount',
        'approve_amount',
        'upfront_interest',
        'disbursed_amount',
        'final_disbursed_amount',
        'invoice_date',
        'funded_date',
        'payment_due_date',
        'interest_start_date',
        'tenor',
        'grace_period',
        'interest_born_by',
        'payment_frequency',
        'interest_rate',
        'overdue_rate',
        'limit_used',
        'limit_released',
        'adhoc_limit_used',
        'adhoc_limit_released',
        'interest_accrued',
        'interest_days',
        'interest_from',
        'interest_to',
        'overdue_accrued',
        'overdue_days',
        'overdue_from',
        'overdue_to',
        'dpd',
        'principal_amount',
        'principal_repayment',
        'principal_waived_off',
        'principal_tds',
        'principal_write_off',
        'principal_refundable',
        'principal_refunded',
        'interest_capitalized',
        'interest_repayment',
        'interest_waived_off',
        'interest_tds',
        'interset_write_off',
        'interest_refundable',
        'interest_refunded',
        'overdue_capitalized',
        'overdue_repayment',
        'overdue_waived_off',
        'overdue_tds',
        'overdue_write_off',
        'overdue_refundable',
        'overdue_refunded',
        'margin_amount',
        'margin_repayment',
        'margin_waived_off',
        'margin_tds',
        'margin_write_off',
        'margin_refundable',
        'margin_refunded',
        'charge_amount',
        'charge_repayment',
        'charge_waived_off',
        'charge_tds',
        'charge_write_off',
        'charge_refundable',
        'charge_refunded',
        'total_outstanding_amount',
        'total_repayment_amount',
        'refundable_amount',
        'refunded_amount',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    public function invoice()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\BizInvoice', 'invoice_id', 'invoice_id');
    }

    public function invoiceDisbursed()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\Lms\InvoiceDisbursed', 'invoice_disbursed_id', 'invoice_disbursed_id');
    }

    public function interestAccrual()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\Lms\InterestAccrual', 'invoice_disbursed_id', 'invoice_disbursed_id');
    }

    public function transactions()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\Lms\Transactions', 'invoice_disbursed_id', 'invoice_disbursed_id');
    }

    public static function saveInvoiceDisbursedDetails($invoice, $whereCondition = []){
        if (!is_array($invoice)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        if (!empty($whereCondition)) {
            if (self::where($whereCondition)->exists()) {
                return self::where($whereCondition)->update($invoice);
            }
        } 
        return self::create($invoice);
    }

    public static function updateInterestAccruedDetails($invoiceDisbursedId){
        $invDisb = InvoiceDisbursed::find($invoiceDisbursedId);
        if($invDisb){
            $intDetails = InterestAccrual::where('invoice_disbursed_id',$invoiceDisbursedId)->whereNotNull('interest_rate')->selectRaw('SUM(`accrued_interest`) as amount, MIN(`interest_date`) AS from_date, MAX(`interest_date`) AS to_date, COUNT(`interest_accrual_id`) AS ttl_days')->get();

            $odDetails = InterestAccrual::where('invoice_disbursed_id','17')->whereNotNull('overdue_interest_rate')->selectRaw('SUM(`accrued_interest`) as amount, MIN(`interest_date`) AS from_date, MAX(`interest_date`) AS to_date, COUNT(`interest_accrual_id`) AS ttl_days')->get();

            $now = Carbon::now();
            $paymentDueDate = Carbon::parse($invDisb->payment_due_date);
            $dpd = $paymentDueDate->diffInDays($now);

            $invDisbDetails = [
                'interest_accrued' => isset($intDetails)?$intDetails->amount:0,
                'interest_days' => isset($intDetails)?$intDetails->ttl_days:0,
                'interest_from' => isset($intDetails)?$intDetails->from_date:0,
                'interest_to' => isset($intDetails)?$intDetails->to_date:0,
                'overdue_accrued' => isset($odDetails)?$odDetails->amount:0,
                'overdue_days' => isset($odDetails)?$odDetails->ttl_days:0,
                'overdue_from' => isset($odDetails)?$odDetails->from_date:0,
                'overdue_to' => isset($odDetails)?$odDetails->to_date:0,
                'dpd' => $dpd,
            ];
            $invDisbDetailsWhere = ['invoice_disbursed_id' => $invoiceDisbursedId];
            self::saveInvoiceDisbursedDetails($invDisbDetails,$invDisbDetailsWhere);
        }
    }

    public static function updatePrincipalTrans($transDetail, $invDisbDetail, $isActionDelete){
        if($transDetail && $invDisbDetail){
            if($transDetail->entry_type){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'principal_repayment' => $invDisbDetail->principal_repayment - $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'principal_repayment' => $invDisbDetail->principal_repayment + $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount + $transDetail->amount,
                    ];
                }
            }else{
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'principal_amount' => $invDisbDetail->principal_amount - $transDetail->amount,
                        'total_outstanding_amount' => $invDisbDetail->total_outstanding_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'principal_amount' => $invDisbDetail->principal_amount + $transDetail->amount,
                        'total_outstanding_amount' => $invDisbDetail->total_outstanding_amount + $transDetail->amount,
                    ];
                }
            }
            $invDisbDetailsWhere = ['invoice_disbursed_id' => $transDetail->invoice_disbursed_id];
            self::saveInvoiceDisbursedDetails($invDisbDetails,$invDisbDetailsWhere);
        }
    }

    public static function updateMarginTrans($transDetail, $invDisbDetail, $isActionDelete){
        if($transDetail && $invDisbDetail){
            if($transDetail->entry_type){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'margin_repayment' => $invDisbDetail->margin_repayment - $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'margin_repayment' => $invDisbDetail->margin_repayment + $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount + $transDetail->amount,
                    ];
                }
            }else{
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'margin_amount' => $invDisbDetail->margin_amount - $transDetail->amount,
                        'total_outstanding_amount' => $invDisbDetail->total_outstanding_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'margin_amount' => $invDisbDetail->margin_amount + $transDetail->amount,
                        'total_outstanding_amount' => $invDisbDetail->total_outstanding_amount + $transDetail->amount,
                    ];
                }
            }
            $invDisbDetailsWhere = ['invoice_disbursed_id' => $transDetail->invoice_disbursed_id];
            self::saveInvoiceDisbursedDetails($invDisbDetails,$invDisbDetailsWhere);
        }
    }

    public static function updateInterestTrans($transDetail, $invDisbDetail, $isActionDelete){
        if($transDetail && $invDisbDetail){
            if($transDetail->entry_type){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'interest_repayment' => $invDisbDetail->interest_repayment - $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'interest_repayment' => $invDisbDetail->interest_repayment + $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount + $transDetail->amount,
                    ];
                }
            }else{
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'interest_capitalized' => $invDisbDetail->interest_capitalized - $transDetail->amount,
                        'total_outstanding_amount' => $invDisbDetail->total_outstanding_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'interest_capitalized' => $invDisbDetail->interest_capitalized + $transDetail->amount,
                        'total_outstanding_amount' => $invDisbDetail->total_outstanding_amount + $transDetail->amount,
                    ];
                }
            }
            $invDisbDetailsWhere = ['invoice_disbursed_id' => $transDetail->invoice_disbursed_id];
            self::saveInvoiceDisbursedDetails($invDisbDetails,$invDisbDetailsWhere);
        }
    }

    public static function updateOverdueTrans($transDetail, $invDisbDetail, $isActionDelete){
        if($transDetail && $invDisbDetail){
            if($transDetail->entry_type){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'overdue_repayment' => $invDisbDetail->overdue_repayment - $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'overdue_repayment' => $invDisbDetail->overdue_repayment + $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount + $transDetail->amount,
                    ];
                }
            }else{
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'overdue_capitalized' => $invDisbDetail->overdue_capitalized - $transDetail->amount,
                        'total_outstanding_amount' => $invDisbDetail->total_outstanding_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'overdue_capitalized' => $invDisbDetail->overdue_capitalized + $transDetail->amount,
                        'total_outstanding_amount' => $invDisbDetail->total_outstanding_amount + $transDetail->amount,
                    ];
                }
            }
            $invDisbDetailsWhere = ['invoice_disbursed_id' => $transDetail->invoice_disbursed_id];
            self::saveInvoiceDisbursedDetails($invDisbDetails,$invDisbDetailsWhere);
        }
    }

    public static function updateChargeTrans($transDetail, $invDisbDetail, $isActionDelete){
        if($transDetail && $invDisbDetail){
            if($transDetail->entry_type && $transDetail->transType->chrg_master_id){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'charge_repayment' => $invDisbDetail->charge_repayment - $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'charge_repayment' => $invDisbDetail->charge_repayment + $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount + $transDetail->amount,
                    ];
                }
            }else{
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'charge_amount' => $invDisbDetail->charge_amount - $transDetail->amount,
                        'total_outstanding_amount' => $invDisbDetail->total_outstanding_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'charge_amount' => $invDisbDetail->charge_amount + $transDetail->amount,
                        'total_outstanding_amount' => $invDisbDetail->total_outstanding_amount + $transDetail->amount,
                    ];
                }
            }
            $invDisbDetailsWhere = ['invoice_disbursed_id' => $transDetail->invoice_disbursed_id];
            self::saveInvoiceDisbursedDetails($invDisbDetails,$invDisbDetailsWhere);
        }
    }

    public static function updateTdsTrans($transDetail, $invDisbDetail, $isActionDelete){
        $pTransDetails = $transDetail->parentTransactions;
        if($pTransDetails && $transDetail->entry_type == 1){
            if($pTransDetails->trans_type == config('lms.TRANS_TYPE.PAYMENT_DISBURSED')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'principal_tds' => $invDisbDetail->principal_tds - $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'principal_tds' => $invDisbDetail->principal_tds + $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount + $transDetail->amount,
                    ];
                }
            }
            elseif($pTransDetails->trans_type == config('lms.TRANS_TYPE.INTEREST')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'interest_tds' => $invDisbDetail->interest_tds - $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'interest_tds' => $invDisbDetail->interest_tds + $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount + $transDetail->amount,
                    ];
                }
            }
            elseif($pTransDetails->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'overdue_tds' => $invDisbDetail->overdue_tds - $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'overdue_tds' => $invDisbDetail->overdue_tds + $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount + $transDetail->amount,
                    ];
                }
            }
            elseif($pTransDetails->trans_type == config('lms.TRANS_TYPE.MARGIN')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'margin_tds' => $invDisbDetail->margin_tds - $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'margin_tds' => $invDisbDetail->margin_tds + $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount + $transDetail->amount,
                    ];
                }
            }
            elseif($pTransDetails->transType->chrg_master_id){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'charge_tds' => $invDisbDetail->charge_tds - $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'charge_tds' => $invDisbDetail->charge_tds + $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount + $transDetail->amount,
                    ];
                }
            }
            $invDisbDetailsWhere = ['invoice_disbursed_id' => $transDetail->invoice_disbursed_id];
            self::saveInvoiceDisbursedDetails($invDisbDetails,$invDisbDetailsWhere);
        }
    }

    public static function updateWaivedOffTrans($transDetail, $invDisbDetail, $isActionDelete){
        $pTransDetails = $transDetail->parentTransactions;
        if($pTransDetails && $transDetail->entry_type == 1){
            if($pTransDetails->trans_type == config('lms.TRANS_TYPE.PAYMENT_DISBURSED')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'principal_waived_off' => $invDisbDetail->principal_waived_off - $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'principal_waived_off' => $invDisbDetail->principal_waived_off + $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount + $transDetail->amount,
                    ];
                }
            }
            elseif($pTransDetails->trans_type == config('lms.TRANS_TYPE.INTEREST')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'interest_waived_off' => $invDisbDetail->interest_waived_off - $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'interest_waived_off' => $invDisbDetail->interest_waived_off + $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount + $transDetail->amount,
                    ];
                }
            }
            elseif($pTransDetails->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'overdue_waived_off' => $invDisbDetail->overdue_waived_off - $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'overdue_waived_off' => $invDisbDetail->overdue_waived_off + $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount + $transDetail->amount,
                    ];
                }
            }
            elseif($pTransDetails->trans_type == config('lms.TRANS_TYPE.MARGIN')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'margin_waived_off' => $invDisbDetail->margin_waived_off - $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'margin_waived_off' => $invDisbDetail->margin_waived_off + $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount + $transDetail->amount,
                    ];
                }
            }
            elseif($pTransDetails->transType->chrg_master_id){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'charge_waived_off' => $invDisbDetail->charge_waived_off - $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'charge_waived_off' => $invDisbDetail->charge_waived_off + $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount + $transDetail->amount,
                    ];
                }
            }
            $invDisbDetailsWhere = ['invoice_disbursed_id' => $transDetail->invoice_disbursed_id];
            self::saveInvoiceDisbursedDetails($invDisbDetails,$invDisbDetailsWhere);
        }
    }

    public static function updateWriteOffTrans($transDetail, $invDisbDetail, $isActionDelete){
        $pTransDetails = $transDetail->parentTransactions;
        if($pTransDetails && $transDetail->entry_type == 1){
            if($pTransDetails->trans_type == config('lms.TRANS_TYPE.PAYMENT_DISBURSED')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'principal_write_off' => $invDisbDetail->principal_write_off - $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'principal_write_off' => $invDisbDetail->principal_write_off + $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount + $transDetail->amount,
                    ];
                }
            }
            elseif($pTransDetails->trans_type == config('lms.TRANS_TYPE.INTEREST')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'interset_write_off' => $invDisbDetail->interset_write_off - $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'interset_write_off' => $invDisbDetail->interset_write_off + $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount + $transDetail->amount,
                    ];
                }
            }
            elseif($pTransDetails->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'overdue_write_off' => $invDisbDetail->overdue_write_off - $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'overdue_write_off' => $invDisbDetail->overdue_write_off + $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount + $transDetail->amount,
                    ];
                }
            }
            elseif($pTransDetails->trans_type == config('lms.TRANS_TYPE.MARGIN')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'margin_write_off' => $invDisbDetail->margin_write_off - $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'margin_write_off' => $invDisbDetail->margin_write_off + $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount + $transDetail->amount,
                    ];
                }
            }
            elseif($pTransDetails->transType->chrg_master_id){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'charge_write_off' => $invDisbDetail->charge_write_off - $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'charge_write_off' => $invDisbDetail->charge_write_off + $transDetail->amount,
                        'total_repayment_amount' => $invDisbDetail->total_repayment_amount + $transDetail->amount,
                    ];
                }
            }
            $invDisbDetailsWhere = ['invoice_disbursed_id' => $transDetail->invoice_disbursed_id];
            self::saveInvoiceDisbursedDetails($invDisbDetails,$invDisbDetailsWhere);
        }
    }

    public static function updateRefundTrans($transDetail, $invDisbDetail, $isActionDelete){
        $pTransDetails = $transDetail->parentTransactions;
        if($pTransDetails && $transDetail->entry_type == 1){
            if($pTransDetails->trans_type == config('lms.TRANS_TYPE.PAYMENT_DISBURSED')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'principal_refundable' => $invDisbDetail->principal_refundable - $transDetail->amount,
                        'refundable_amount' => $invDisbDetail->refundable_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'principal_refundable' => $invDisbDetail->principal_refundable + $transDetail->amount,
                        'refundable_amount' => $invDisbDetail->refundable_amount + $transDetail->amount,
                    ];
                }
            }
            elseif($pTransDetails->trans_type == config('lms.TRANS_TYPE.INTEREST')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'interest_refundable' => $invDisbDetail->interest_refundable - $transDetail->amount,
                        'refundable_amount' => $invDisbDetail->refundable_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'interest_refundable' => $invDisbDetail->interest_refundable + $transDetail->amount,
                        'refundable_amount' => $invDisbDetail->refundable_amount + $transDetail->amount,
                    ];
                }
            }
            elseif($pTransDetails->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'overdue_refundable' => $invDisbDetail->overdue_refundable - $transDetail->amount,
                        'refundable_amount' => $invDisbDetail->refundable_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'overdue_refundable' => $invDisbDetail->overdue_refundable + $transDetail->amount,
                        'refundable_amount' => $invDisbDetail->refundable_amount + $transDetail->amount,
                    ];
                }
            }
            elseif($pTransDetails->trans_type == config('lms.TRANS_TYPE.MARGIN')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'margin_refundable' => $invDisbDetail->margin_refundable - $transDetail->amount,
                        'refundable_amount' => $invDisbDetail->refundable_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'margin_refundable' => $invDisbDetail->margin_refundable + $transDetail->amount,
                        'refundable_amount' => $invDisbDetail->refundable_amount + $transDetail->amount,
                    ];
                }
            }
            elseif($pTransDetails->transType->chrg_master_id){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'charge_refundable' => $invDisbDetail->charge_refundable - $transDetail->amount,
                        'refundable_amount' => $invDisbDetail->refundable_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'charge_refundable' => $invDisbDetail->charge_refundable + $transDetail->amount,
                        'refundable_amount' => $invDisbDetail->refundable_amount + $transDetail->amount,
                    ];
                }
            }
            $invDisbDetailsWhere = ['invoice_disbursed_id' => $transDetail->invoice_disbursed_id];
            self::saveInvoiceDisbursedDetails($invDisbDetails,$invDisbDetailsWhere);
        }
        if($pTransDetails && $transDetail->entry_type == 0){
            if($pTransDetails->trans_type == config('lms.TRANS_TYPE.PAYMENT_DISBURSED')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'principal_refunded' => $invDisbDetail->principal_refunded - $transDetail->amount,
                        'refunded_amount' => $invDisbDetail->refunded_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'principal_refunded' => $invDisbDetail->principal_refunded + $transDetail->amount,
                        'refunded_amount' => $invDisbDetail->refunded_amount + $transDetail->amount,
                    ];
                }
            }
            elseif($pTransDetails->trans_type == config('lms.TRANS_TYPE.INTEREST')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'interest_refunded' => $invDisbDetail->interest_refunded - $transDetail->amount,
                        'refunded_amount' => $invDisbDetail->refunded_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'interest_refunded' => $invDisbDetail->interest_refunded + $transDetail->amount,
                        'refunded_amount' => $invDisbDetail->refunded_amount + $transDetail->amount,
                    ];
                }
            }
            elseif($pTransDetails->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'overdue_refunded' => $invDisbDetail->overdue_refunded - $transDetail->amount,
                        'refunded_amount' => $invDisbDetail->refunded_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'overdue_refunded' => $invDisbDetail->overdue_refunded + $transDetail->amount,
                        'refunded_amount' => $invDisbDetail->refunded_amount + $transDetail->amount,
                    ];
                }
            }
            elseif($pTransDetails->trans_type == config('lms.TRANS_TYPE.MARGIN')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'margin_refunded' => $invDisbDetail->margin_refunded - $transDetail->amount,
                        'refunded_amount' => $invDisbDetail->refunded_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'margin_refunded' => $invDisbDetail->margin_refunded + $transDetail->amount,
                        'refunded_amount' => $invDisbDetail->refunded_amount + $transDetail->amount,
                    ];
                }
            }
            elseif($pTransDetails->transType->chrg_master_id){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'charge_refunded' => $invDisbDetail->charge_refunded - $transDetail->amount,
                        'refunded_amount' => $invDisbDetail->refunded_amount - $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'charge_refunded' => $invDisbDetail->charge_refunded + $transDetail->amount,
                        'refunded_amount' => $invDisbDetail->refunded_amount + $transDetail->amount,
                    ];
                }
            }
            $invDisbDetailsWhere = ['invoice_disbursed_id' => $transDetail->invoice_disbursed_id];
            self::saveInvoiceDisbursedDetails($invDisbDetails,$invDisbDetailsWhere);
        }
    }

    public static function updateReverseTrans($transDetail, $invDisbDetail, $isActionDelete){
        $lTransDetails = $transDetail->linkTransactions;
        $pTransDetails = $transDetail->parentTransactions;
        if($lTransDetails && $transDetail->entry_type == 1){
            if($lTransDetails->trans_type == config('lms.TRANS_TYPE.PAYMENT_DISBURSED')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'principal_amount' => $invDisbDetail->principal_amount + $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'principal_amount' => $invDisbDetail->principal_amount - $transDetail->amount,
                    ];
                }
            }
            elseif($lTransDetails->trans_type == config('lms.TRANS_TYPE.INTEREST')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'interest_capitalized' => $invDisbDetail->interest_capitalized + $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'interest_capitalized' => $invDisbDetail->interest_capitalized - $transDetail->amount,
                    ];
                }
            }
            elseif($lTransDetails->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'overdue_capitalized' => $invDisbDetail->overdue_capitalized + $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'overdue_capitalized' => $invDisbDetail->overdue_capitalized - $transDetail->amount,
                    ];
                }
            }
            elseif($lTransDetails->trans_type == config('lms.TRANS_TYPE.MARGIN')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'margin_amount' => $invDisbDetail->margin_amount + $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'margin_amount' => $invDisbDetail->margin_amount - $transDetail->amount,
                    ];
                }
            }
            elseif($lTransDetails->transType->chrg_master_id){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'charge_amount' => $invDisbDetail->charge_amount + $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'charge_amount' => $invDisbDetail->charge_amount - $transDetail->amount,
                    ];
                }
            }
            if ($isActionDelete) {
                $invDisbDetails['total_outstanding_amount'] = $invDisbDetail->total_outstanding_amount + $transDetail->amount;

                if($lTransDetails->payment_id || $pTransDetails->payment_id){
                    $invDisbDetails['total_repayment_amount'] = $invDisbDetail->total_repayment_amount - $transDetail->amount;
                }
            } else {
                $invDisbDetails['total_outstanding_amount'] = $invDisbDetail->total_outstanding_amount - $transDetail->amount;
            
                if($lTransDetails->payment_id || $pTransDetails->payment_id){
                    $invDisbDetails['total_repayment_amount'] = $invDisbDetail->total_repayment_amount + $transDetail->amount;
                }
            }

            $invDisbDetailsWhere = ['invoice_disbursed_id' => $transDetail->invoice_disbursed_id];
            self::saveInvoiceDisbursedDetails($invDisbDetails,$invDisbDetailsWhere);
        }
        
        if($lTransDetails && $transDetail->entry_type == 0){
            if($lTransDetails->trans_type == config('lms.TRANS_TYPE.PAYMENT_DISBURSED')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'principal_repayment' => $invDisbDetail->principal_repayment + $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'principal_repayment' => $invDisbDetail->principal_repayment - $transDetail->amount,
                    ];
                }
            }
            elseif($lTransDetails->trans_type == config('lms.TRANS_TYPE.INTEREST')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'interest_repayment' => $invDisbDetail->interest_repayment + $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'interest_repayment' => $invDisbDetail->interest_repayment - $transDetail->amount,
                    ];
                }
            }
            elseif($lTransDetails->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'overdue_repayment' => $invDisbDetail->overdue_repayment + $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'overdue_repayment' => $invDisbDetail->overdue_repayment - $transDetail->amount,
                    ];
                }
            }
            elseif($lTransDetails->trans_type == config('lms.TRANS_TYPE.MARGIN')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'margin_repayment' => $invDisbDetail->margin_repayment + $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'margin_repayment' => $invDisbDetail->margin_repayment - $transDetail->amount,
                    ];
                }
            }
            elseif($lTransDetails->transType->chrg_master_id){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'charge_repayment' => $invDisbDetail->charge_repayment + $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'charge_repayment' => $invDisbDetail->charge_repayment - $transDetail->amount,
                    ];
                }
            }

            if ($isActionDelete) {
                $invDisbDetails['total_outstanding_amount'] = $invDisbDetail->total_outstanding_amount - $transDetail->amount;

                if($lTransDetails->payment_id || $pTransDetails->payment_id){
                    $invDisbDetails['total_repayment_amount'] = $invDisbDetail->total_repayment_amount + $transDetail->amount;
                }
            } else {
                $invDisbDetails['total_outstanding_amount'] = $invDisbDetail->total_outstanding_amount + $transDetail->amount;

                if($lTransDetails->payment_id || $pTransDetails->payment_id){
                    $invDisbDetails['total_repayment_amount'] = $invDisbDetail->total_repayment_amount - $transDetail->amount;
                }
            }

            $invDisbDetailsWhere = ['invoice_disbursed_id' => $transDetail->invoice_disbursed_id];
            self::saveInvoiceDisbursedDetails($invDisbDetails,$invDisbDetailsWhere);
        }
    }

    public static function updateCancelTrans($transDetail, $invDisbDetail, $isActionDelete){
        $lTransDetails = $transDetail->linkTransactions;
        $pTransDetails = $transDetail->parentTransactions;
        if($lTransDetails && $transDetail->entry_type == 1){
            if($lTransDetails->trans_type == config('lms.TRANS_TYPE.PAYMENT_DISBURSED')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'principal_amount' => $invDisbDetail->principal_amount + $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'principal_amount' => $invDisbDetail->principal_amount - $transDetail->amount,
                    ];
                }
            }
            elseif($lTransDetails->trans_type == config('lms.TRANS_TYPE.INTEREST')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'interest_capitalized' => $invDisbDetail->interest_capitalized + $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'interest_capitalized' => $invDisbDetail->interest_capitalized - $transDetail->amount,
                    ];
                }
            }
            elseif($lTransDetails->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'overdue_capitalized' => $invDisbDetail->overdue_capitalized + $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'overdue_capitalized' => $invDisbDetail->overdue_capitalized - $transDetail->amount,
                    ];
                }
            }
            elseif($lTransDetails->trans_type == config('lms.TRANS_TYPE.MARGIN')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'margin_amount' => $invDisbDetail->margin_amount + $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'margin_amount' => $invDisbDetail->margin_amount - $transDetail->amount,
                    ];
                }
            }
            elseif($lTransDetails->transType->chrg_master_id){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'charge_amount' => $invDisbDetail->charge_amount + $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'charge_amount' => $invDisbDetail->charge_amount - $transDetail->amount,
                    ];
                }
            }

            if ($isActionDelete) {
                $invDisbDetails['total_outstanding_amount'] = $invDisbDetail->total_outstanding_amount + $transDetail->amount;

                if($lTransDetails->payment_id || $pTransDetails->payment_id){
                    $invDisbDetails['total_repayment_amount'] = $invDisbDetail->total_repayment_amount - $transDetail->amount;
                }
            } else {
                $invDisbDetails['total_outstanding_amount'] = $invDisbDetail->total_outstanding_amount - $transDetail->amount;

                if($lTransDetails->payment_id || $pTransDetails->payment_id){
                    $invDisbDetails['total_repayment_amount'] = $invDisbDetail->total_repayment_amount + $transDetail->amount;
                }
            }

            $invDisbDetailsWhere = ['invoice_disbursed_id' => $transDetail->invoice_disbursed_id];
            self::saveInvoiceDisbursedDetails($invDisbDetails,$invDisbDetailsWhere);
        }
        
        if($lTransDetails && $transDetail->entry_type == 0){
            if($lTransDetails->trans_type == config('lms.TRANS_TYPE.PAYMENT_DISBURSED')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'principal_repayment' => $invDisbDetail->principal_repayment + $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'principal_repayment' => $invDisbDetail->principal_repayment - $transDetail->amount,
                    ];
                }
            }
            elseif($lTransDetails->trans_type == config('lms.TRANS_TYPE.INTEREST')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'interest_repayment' => $invDisbDetail->interest_repayment + $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'interest_repayment' => $invDisbDetail->interest_repayment - $transDetail->amount,
                    ];
                }
            }
            elseif($lTransDetails->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'overdue_repayment' => $invDisbDetail->overdue_repayment + $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'overdue_repayment' => $invDisbDetail->overdue_repayment - $transDetail->amount,
                    ];
                }
            }
            elseif($lTransDetails->trans_type == config('lms.TRANS_TYPE.MARGIN')){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'margin_repayment' => $invDisbDetail->margin_repayment + $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'margin_repayment' => $invDisbDetail->margin_repayment - $transDetail->amount,
                    ];
                }
            }
            elseif($lTransDetails->transType->chrg_master_id){
                if ($isActionDelete) {
                    $invDisbDetails = [
                        'charge_repayment' => $invDisbDetail->charge_repayment + $transDetail->amount,
                    ];
                } else {
                    $invDisbDetails = [
                        'charge_repayment' => $invDisbDetail->charge_repayment - $transDetail->amount,
                    ];
                }
            }

            if ($isActionDelete) {
                $invDisbDetails['total_outstanding_amount'] = $invDisbDetail->total_outstanding_amount - $transDetail->amount;

                if($lTransDetails->payment_id || $pTransDetails->payment_id){
                    $invDisbDetails['total_repayment_amount'] = $invDisbDetail->total_repayment_amount + $transDetail->amount;
                }
            } else {
                $invDisbDetails['total_outstanding_amount'] = $invDisbDetail->total_outstanding_amount + $transDetail->amount;

                if($lTransDetails->payment_id || $pTransDetails->payment_id){
                    $invDisbDetails['total_repayment_amount'] = $invDisbDetail->total_repayment_amount - $transDetail->amount;
                }
            }

            $invDisbDetailsWhere = ['invoice_disbursed_id' => $transDetail->invoice_disbursed_id];
            self::saveInvoiceDisbursedDetails($invDisbDetails,$invDisbDetailsWhere);
        }
    }
  
    public static function createTransactionDetails($transDetails)
    {
        self::processTransDetails($transDetails);
    }

    public static function updateTransactionDetails($transDetails)
    {
        //
    }

    public static function deleteTransactionDetails($transDetails)
    {
        self::processTransDetails($transDetails, $isActionDelete = true);
    }

    public static function forceDeletedTransactionDetails($transDetails)
    {
        //
    }

    private static function processTransDetails($transDetails, $isActionDelete = false)
    {
        if($transDetails && $transDetails->invoice_disbursed_id){
            $invDisbDetail = self::where('invoice_disbursed_id', $transDetails->invoice_disbursed_id)->first();
            if ($invDisbDetail) {
                switch ($transDetails->trans_type){
                    case config('lms.TRANS_TYPE.PAYMENT_DISBURSED'):
                        self::updatePrincipalTrans($transDetails, $invDisbDetail, $isActionDelete);
                        break;
                    case config('lms.TRANS_TYPE.MARGIN'):
                        self::updateMarginTrans($transDetails, $invDisbDetail, $isActionDelete);
                        break;
                    case config('lms.TRANS_TYPE.INTEREST'):
                        self::updateInterestTrans($transDetails, $invDisbDetail, $isActionDelete);
                        break;
                    case config('lms.TRANS_TYPE.INTEREST_OVERDUE'):
                        self::updateOverdueTrans($transDetails, $invDisbDetail, $isActionDelete);
                        break;
                    case config('lms.TRANS_TYPE.TDS'):
                        self::updateTdsTrans($transDetails, $invDisbDetail, $isActionDelete);
                        break;
                    case config('lms.TRANS_TYPE.WAVED_OFF'):
                        self::updateWaivedOffTrans($transDetails, $invDisbDetail, $isActionDelete);
                        break;
                    case config('lms.TRANS_TYPE.WRITE_OFF'):
                        self::updateWriteOffTrans($transDetails, $invDisbDetail, $isActionDelete);
                        break;
                    case config('lms.TRANS_TYPE.REFUND'):
                        self::updateRefundTrans($transDetails, $invDisbDetail, $isActionDelete);
                        break;
                    case config('lms.TRANS_TYPE.REVERSE'):
                        self::updateReverseTrans($transDetails, $invDisbDetail, $isActionDelete);
                        break;
                    case config('lms.TRANS_TYPE.CANCEL'):
                        self::updateCancelTrans($transDetails, $invDisbDetail, $isActionDelete);
                        break;
                    default:
                        self::updateChargeTrans($transDetails, $invDisbDetail, $isActionDelete);
                        break;
                }
            }
        }
    }
}
