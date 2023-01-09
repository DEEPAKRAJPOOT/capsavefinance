<?php

namespace App\Inv\Repositories\Models\Lms;

use Helpers;
use Illuminate\Database\Eloquent\Model;
use App\Inv\Repositories\Factory\Models\BaseModel;

class Apportionment extends BaseModel
{
    /* The database table used by the model.
    *
    * @var string
    */
    protected $table = 'apportionment';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'apportionment_id';

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
    public $userstamps = true;

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'apportionment_type',
        'payment_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    
    public function payment(){
        return $this->hasOne('App\Inv\Repositories\Models\Payment','payment_id','payment_id');
    }

    public function transaction(){
        return $this->belongsTo('App\Inv\Repositories\Models\Lms\Transactions', 'apportionment_id', 'apportionment_id');
    }

    /**
     * Save Apportionment Details
     * 
     * @param array $apportionment
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function saveApportionment($apportionment,$whereCondition=[])
    {
        if (!is_array($apportionment)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        if (!empty($whereCondition)) {
            return self::where($whereCondition)->update($apportionment);
        }else{
            return self::create($apportionment);
        }
    }
}