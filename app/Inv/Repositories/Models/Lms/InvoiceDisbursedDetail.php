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

    protected $table = 'invoice_disbursed_detail';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'invoice_disbursed_detail_id';

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
        
        'interest_capitalized',
        'interest_repayment',
        'interest_waived_off',
        'interest_tds',
        'interset_write_off',
        
        'overdue_capitalized',
        'overdue_repayment',
        'overdue_waived_off',
        'overdue_tds',
        'overdue_write_off',
        
        'margin_amount',
        'margin_repayment',
        'margin_waived_off',
        'margin_tds',
        'margin_write_off',
        
        'charge_amount',
        'charge_repayment',
        'charge_waived_off',
        'charge_tds',
        'charge_write_off',
        
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

    public static function saveInvoiceDisbursedDetails($invoice, $whereCondition = [])
    {
        if (!is_array($invoice)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        if (!empty($whereCondition)) {
            if (self::where($whereCondition)->count() > 0) {
                return self::where($whereCondition)->update($invoice);
            }
        } 
        return self::create($invoice);
    }

}
