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
        'user_id',
        'biz_id',
        'prgm_offer_id',
        'status',
        'limit_amt',
        'start_date',
        'end_date',
        'file_id',
        'remark',
        'created_at',
        'created_by',
        'updated_at',        
        'updated_by',
    ];

    public static function saveAppOfferAdhocLimit($data, $id){
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        if (!is_null($id)) {
            return self::where('app_offer_adhoc_limit_id', $id)->update($data);
        } else {
            return self::create($data);
        }
    }

    public function prgm_offer(){
        return $this->belongsTo('App\Inv\Repositories\Models\AppProgramOffer','prgm_offer_id','prgm_offer_id');
    }

    public static function checkUserAdhoc($attr)
    {
        $mytime = Carbon::now();
        $dateTime  =  $mytime->toDateTimeString();
        return self::where(['user_id' => $attr['user_id'],'prgm_offer_id' => $attr['prgm_offer_id'],'status' => 1])->whereRaw('CAST("'.$dateTime.'" AS DATE) between `start_date` and `end_date`')->sum('limit_amt');
    }

    public function file(){
        return $this->belongsTo('App\Inv\Repositories\Models\UserFile', 'file_id', 'file_id');
    }
    
    public function adhocDocument(){
        return $this->hasmany('App\Inv\Repositories\Models\OfferAdhocDocument', 'offer_adhoc_limit_id', 'app_offer_adhoc_limit_id');
    }
}
