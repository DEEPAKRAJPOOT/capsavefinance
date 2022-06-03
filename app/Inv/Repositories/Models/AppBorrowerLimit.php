<?php

namespace App\Inv\Repositories\Models;

use DB;
use Carbon\Carbon;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\AppProgramLimit;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\Master\BorrowerLimit;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class AppBorrowerLimit extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'app_borrower_limit';


    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'app_limit_id';


    /**
     * Maintain created_at and updated_at automatically
     *
     * @var boolean
     */
    public $timestamps = true;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'app_limit_id',
        'borrower_limit_id',
        'app_id',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by',
        'is_deleted'
    ];

     /*
     * save data in table
     */

    public static function creates($attributes) {
        self::create($attributes);
        return true;
    }

    public static function getAppBorrowerLimit($appId){
       $result =  self::with('borrowerLimit')->where('app_id',$appId)->first();
       return $result?$result:false;
    }

    public function borrowerLimit(){
        return $this->hasOne(BorrowerLimit::class,'limit_id','borrower_limit_id');
    }
}
