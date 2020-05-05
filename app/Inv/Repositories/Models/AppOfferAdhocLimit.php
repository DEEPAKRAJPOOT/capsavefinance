<?php

namespace App\Inv\Repositories\Models;

use DB;
use Carbon\Carbon;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\AppProgramLimit;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class AppOfferAdhocLimit extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'app_offer_adhoc_limit';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'app_offer_adhoc_limit_id';

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
        'user_id',
        'biz_id',
        'prgm_offer_id',
        'status',
        'limit_amt',
        'start_date',
        'end_date',
        'created_at',
        'created_by',
        'updated_at',        
        'updated_by',
    ];

    
    public static function checkUserAdhoc($attr)
    {
        $mytime = Carbon::now();
        $dateTime  =  $mytime->toDateTimeString();
        return self::where(['user_id' => $attr['user_id'],'prgm_offer_id' => $attr['prgm_offer_id'],'status' => 1])->whereRaw('"'.$dateTime.'" between `start_date` and `end_date`') ->sum('limit_amt');
       
    }
}
