<?php

namespace App\Inv\Repositories\Models\ETL;

use App\Inv\Repositories\Factory\Models\BaseModel;

class OverdueReport extends BaseModel
{
    
    protected $connection = 'mysql2';
    protected $table = 'etl_overdue_report';
    public $timestamps = false;
    public $userstamps = false;

    protected $fillable = [
        "Customer Name",
        "Customer ID",
        "Invoice No",
        "Invoice Due Date",
        "Virtual Account",
        "Sanction Limit",
        "Limit Available",
        "O/s Amount",
        "Over Due Days",
        "Overdue Amount",
        "Sales Person Name"
    ];
}