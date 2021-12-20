<?php

namespace App\Inv\Repositories\Models\ETL;

use App\Inv\Repositories\Factory\Models\BaseModel;

class DisbursalReport extends BaseModel
{
    
    protected $connection = 'mysql2';
    protected $table = 'etl_disbursal_report';
    public $timestamps = false;
    public $userstamps = false;

    protected $fillable = [
        "Borrower name",
        "RM",
        "Anchor name",
        "Anchor program name",
        "Vendor_Beneficiary Name",
        "Region",
        "Sanction no",
        "Sanction Date",
        "Sanction Amount",
        "Status",
        "Disbrusal Month",
        "Disburse amount",
        "Disbursement date",
        "Disbursal UTR No",
        "Disbursal Act No",
        "Disbursal IFSC Code",
        "Type of Finance",
        "Supply chain type",
        "Tenure",
        "Interest rate",
        "Interest amount",
        "From_1",
        "To_1",
        "TDS on Interest",
        "Net Interest",
        "Interest received date",
        "Processing fees",
        "Processing amount",
        "Processing fee with GST",
        "TDS on Processing fee",
        "Net Processing fee receivable",
        "Processing fee received",
        "Processing Fee Amount received date",
        "Balance",
        "Margin",
        "Due date",
        "Funds_rec From anchor_client",
        "Principal receivable",
        "Received",
        "Net Receivable",
        "Adhoc interest",
        "Net Disbursement",
        "Gross",
        "Net of interest_PF _Stamp",
    ];
}