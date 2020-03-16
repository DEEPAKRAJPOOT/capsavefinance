<?php

namespace App\Inv\Repositories\Models\Financial;

use App\Inv\Repositories\Factory\Models\BaseModel;

class FinancialDisbursal extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'disbursal';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'disbursal_id';

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
      'user_id',
      'app_id',
      'invoice_id',
      'prgm_offer_id',
      'disburse_date',
      'bank_account_id',
      'bank_name',
      'ifsc_code',
      'acc_no',
      'virtual_acc_id',
      'customer_id',
      'principal_amount',
      'inv_due_date',
      'payment_due_date',
      'tenor_days',
      'interest_rate',
      'total_interest',
      'margin',
      'disburse_amount',
      'total_repaid_amt',
      'status_id',
      'disbursal_api_log_id',
      'disburse_type',
      'settlement_amount',
      'settlement_date',
      'surplus_amount',
      'accured_interest',
      'interest_refund',
      'funded_date',
      'int_accrual_start_dt',
      'processing_fee',
      'grace_period',
      'overdue_interest_rate',
      'penal_interest',
      'repayment_amount',
      'total_repaid_amount',
      'penal_days',
      'penalty_amount',
      'created_by',
      'created_at',
      'updated_by',
      'updated_at',       
    ];
}
