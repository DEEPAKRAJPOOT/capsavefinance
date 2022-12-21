<?php

namespace App\Inv\Repositories\Models;
use App\Inv\Repositories\Factory\Models\BaseModel;

class TransFactVoucher extends BaseModel
{
     /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'trans_fact_voucher';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'id';
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
        'tally_id',
        'trans_id',
        'fact_voucher_no',
        'created_at'
    ];

    public static function getTransFactVoucher($trans_id){

        $result = self::where('trans_id',$trans_id)->first();
        return $result?$result:false;
    }

}