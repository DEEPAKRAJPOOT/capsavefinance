<?php

namespace App\Inv\Repositories\Models\ETL;

use App\Inv\Repositories\Factory\Models\BaseModel;

class MaturityReport extends BaseModel
{
    
    protected $connection = 'mysql2';
    protected $table = 'etl_maturity_report';
    public $timestamps = false;
    public $userstamps = false;

    protected $fillable = [
        "Customer Name",
        "Loan Account",
        "Virtual Account",
        "Transction Date",
        "Tranction",
        "Invoice",
        "Invoice Date",
        "Invoice Amount",
        "Margin Amount",
        "Amount Disbursed",
        "O/s Amount",
        "O/s Days",
        "Credit Period",
        "Maturity Date",
        "Maturity Amount",
        "Over Due Days",
        "Overdue Amount",
        "Remark while uploading Invoice"
    ];
}