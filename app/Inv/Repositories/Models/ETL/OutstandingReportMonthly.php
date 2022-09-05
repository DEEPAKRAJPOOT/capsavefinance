<?php

namespace App\Inv\Repositories\Models\ETL;

use App\Inv\Repositories\Factory\Models\BaseModel;

class OutstandingReportMonthly extends BaseModel
{
    
    protected $connection = 'mysql2';
    protected $table = 'etl_outstanding_report_monthly';
    public $timestamps = false;
    public $userstamps = false;

    protected $fillable = [
        'Batch No',
        'Customer Name',
        'Customer ID',
        'Anchor Name',
        'Invoice No',
        'Date of Disbursement',
        'Invoice Amount',
        'Invoice Approved Amount',
        'Margin',
        'Upfront Interest Deducted',
        'Invoice Level Charges Deducted If Any',
        'Invoice Level Charges Applied If Any',
        'Invoice Disbursement Amount',
        'Interest Frequency',
        'Interest Amount Posted',
        'Disbursement Method Net or Gross',
        'Invoice Due Date',
        'Virtual Account No',
        'Tenure',
        'ROI',
        'ODI Interest',
        'Principal Outstanding',
        'Interest',
        'Overdue Interest Posted',
        'Overdue Interest Outstanding',
        'Invoice Level Charges Outstanding',
        'Total Outstanding',
        'Grace Days Interest',
        'Grace Days Principle',
        'Principle Overdue',
        'Principle Overdue Category',
        'Principle DPD',
        'Interest DPD',
        'Final DPD',
        'Outstanding Max Bucket',
        'Maturity Days',
        'Maturity Bucket',
        'Balance Margin to be Refunded',
        'Balance Interest to be refunded',
        'Balance Overdue Interest to be refunded'
    ];
}