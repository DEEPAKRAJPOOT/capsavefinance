<?php

namespace App\Inv\Repositories\Models;

use DB;
use Carbon\Carbon;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class UserCkycConsent extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'user_ckyc_consent';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'ckyc_consent_id';

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
        'consent_type',
        'user_id',
        'file_id',
        'app_id',
        'biz_owner_id',
        'comment',
        'otp_trans_id',
        'user_ucic_id',
        'status',
        'created_at',
        'created_by',
        'updated_at',        
        'updated_by'
    ];

    public static function saveOtpConsent($data, $ckyc_consent_id){
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        if (!is_null($ckyc_consent_id)) {
            return self::where('ckyc_consent_id', $ckyc_consent_id)->update($data);
        } else {
            return self::create($data);
        }
    }

    public static function updateotpConsent($data, $where){

        if (!is_array($data) && !is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        return self::where($where)->update($data);
    }

    public static function updateBusinessotpConsent($data, $user_id){

        if (!is_array($data) && !is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        return self::where(['user_id'=>$user_id])->whereNull('biz_owner_id')->update($data);
    }

    public static function updateConsentByConsentId($ckyc_consent_id,$consentData){

        if (!is_array($consentData)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        return self::where(['ckyc_consent_id'=>(int)$ckyc_consent_id])
                    ->update($consentData);

    }

    public static function updateConsentByuserId($where,$consentData){

        if (!is_array($consentData) && !is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        if(isset($where['biz_owner_id']) && !empty($where['biz_owner_id'])){
            if(self::where($where)->count()){

                return self::where($where)->update($consentData);

            }else{

                $consentData['biz_owner_id'] = $where['biz_owner_id'];
                $consentData['user_id'] = $where['user_id'];
                return self::create($consentData);
            }
        }else{
            if(self::where($where)->whereNull('biz_owner_id')->count()){

                return self::where($where)->whereNull('biz_owner_id')->update($consentData);
            }else{

                $consentData['user_id'] = $where['user_id'];
                return self::create($consentData);
            }
        }
    }

    public static function getUserConsent($where){

        if(isset($where['biz_owner_id']) && !empty($where['biz_owner_id'])){

            return self::where($where)->count();
        }else{

            return self::where($where)->whereNull('biz_owner_id')->count();
        }
    }

}