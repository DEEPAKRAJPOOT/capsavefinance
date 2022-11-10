<?php

namespace App\Inv\Repositories\Models;
use App\Inv\Repositories\Factory\Models\BaseModel;

class TallyFactVoucher extends BaseModel
{

    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'tally_fact_voucher';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'fact_voucher_id';
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
    public $userstamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tally_id',
        'fact_year1',
        'fact_year2',
        'fact_month',
        'fact_srp_seq_number',
        'fact_sjv_seq_number',
        'created_at',
        'updated_at' 
    ];

    public static function getfactVoucherNumber(){

        return self::select('*')
            ->orderBy('fact_voucher_id','DESC')
            ->first();
    }
}
