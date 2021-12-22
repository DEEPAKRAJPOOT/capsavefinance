<?php

namespace App\Inv\Repositories\Models\ETL;

use App\Inv\Repositories\Factory\Models\BaseModel;

class AccountDisbursalReport extends BaseModel
{
    
    protected $connection = 'mysql2';
    protected $table = 'etl_account_disbursal';
    public $timestamps = false;
    public $userstamps = false;

    protected $fillable = [
        "Customer Name",
        "Loan Account",
        "Transction Date",
        "Tranction",
        "Invoice",
        "Invoice Date",
        "Invoice Amount",
        "Margin Amount",
        "Amount Disbrused",
        "UTR",
        "Remark while uploading Invoice",
        "Beneficiary Credit Account No",
        "Beneficiary IFSC Code",
        "Status",
        "Status Description"
    ];
}