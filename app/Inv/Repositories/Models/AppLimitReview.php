<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class AppLimitReview extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'app_limit_review';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'app_limit_review_id';

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
    public $userstamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'app_limit_review_id',
        'app_limit_id',
        'review_date',
        'file_id',
        'comment_txt',
        'status',
        'created_at',
        'created_by',
    ];

    public static function saveAppLimitReview($data){
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        return self::create($data);
    }

    public static function updateAppLimitReview($data, $whereCond=[]){
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        if (!is_array($whereCond)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }        
        
        if (count($whereCond) > 0) {
            return self::where($whereCond)->update($data);
        }
    }    

    public function appLimit(){
        return $this->hasMany('App\Inv\Repositories\Models\AppLimit','app_limit_id','app_limit_id');
    }
   
    
    public static  function getAppReviewLimit($user_id)
    {
        $result = self::select('app_limit_review.*')
        ->join('app_limit', 'app_limit.app_limit_id', '=', 'app_limit_review.app_limit_id') 
        ->where('app_limit.user_id', $user_id)
        ->orderBy('app_limit_review.created_at','DESC')
        ->get();
       return $result ?: false;
    }

    public static  function appLimitReviewByUserId($user_id)
    {
        return  self::where(['user_id'=>$user_id])
                ->where('status', 1)
                ->first();
    }

    public static function getAppLimitReviewData($whereCond)
    {
        $result = self::select('*')
                ->where($whereCond)               
                ->get();
        return $result ? $result : []; 
    }

    public static function getAppReviewLimitLatestData($whereCond)
    {
        return  self::where($whereCond)
                    ->latest()
                    ->get()
                    ->first();;
    }

}
