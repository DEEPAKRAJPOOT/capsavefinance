<?php

namespace App\Inv\Repositories\Models\ETL;

use App\Inv\Repositories\Factory\Models\BaseModel;

class UtilizationReport extends BaseModel
{
    
    protected $connection = 'mysql2';
    protected $table = 'etl_utilization_report';
    public $timestamps = false;
    public $userstamps = false;

    protected $fillable = [
        "Anchor Name",
        "Program Name",
        "Sub Program Name",
        "No of Clients Sanctioned",
        "No of Over Due Customer",
        "Total Over Due Amount",
        "Client Name",
        "Customer ID",
        "Virtual Account No",
        "Client Sanction Limit",
        "Limit Utilized Limit",
        "Available Limit",
        "Expiry Date",
        "Sales Person Name",
        "Invoice No",
        "Invoice Date",
        "Invoice Amount",
        "Invoice Approved",
        "Margin Amount",
        "Amount Disbursed",
        "Principal OverDue Days",
        "Principal OverDue Amount",
        "Over Due Days",
        "Over Due Interest Amount"
    ];
}