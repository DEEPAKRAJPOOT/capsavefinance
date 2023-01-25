<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use Session;
use Auth;
use DB;
 

class FactJournalEntry extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fact_journal_entry';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'fact_journal_entry_id';

    /**
     * Maintain created_at and updated_at automatically
     *
     * @var boolean
     */

    public $timestamps = false;


    /**
     * Maintain created_by and updated_by automatically
     *
     * @var boolean
     */
    public $userstamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'voucher_no',
        'voucher_date',
        'voucher_narration',
        'general_ledger_code',
        'document_class',
        'd_/_c',
        'amount',
        'description',
        'item_serial_number',
        'tax_code',
        'name',
        'gST_hSN_code',
        'sAC_code',
        'gST_state_name',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'city',
        'country',
        'postal_code',
        'telephone_number',
        'mobile_phone_number',
        'fAX',
        'email',
        'gST_identification_number_(GSTIN)',
    ];

    /**
     * get Tally Data
     * 
     * @param type $where array
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    
}
