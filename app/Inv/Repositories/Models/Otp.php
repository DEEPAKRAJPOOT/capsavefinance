<?php

namespace App\Inv\Repositories\Models;

use Carbon\Carbon;
use DateTime;
use App\Inv\Repositories\Factory\Models\BaseModel;

class Otp extends BaseModel
{
    /* The database table used by the model.
     *
     * @var string
     */
    protected $table = 'otp_trans';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'otp_trans_id';

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
        'user_id',
        'activity_id',
        'otp_no',
        'otp_creation_time',
        'otp_exp_time',
        'is_otp_expired',
        'is_otp_resent',
        'is_verified',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    /**
     * Save Otp
     *
     * @param  array $arrOtp
     *
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions
     */
    public static function saveOtp($arrOtp = [])
    {
        //Check data is Array
        if (!is_array($arrOtp)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        //Check data is not blank
        if (empty($arrOtp)) {
            throw new BlankDataExceptions(trans('error_message.data_not_found'));
        }
        
        /**
         * Create Otp
         */
        $objotp = self::create($arrOtp);

        return ($objotp->otp_trans_id ?: false);
    }

    /**
     * Get Otp
     *
     * @param  integer $userId
     *
     * @return mixed Array | Boolean false
     * @throws InvalidDataTypeExceptions
     */
    public static function getOtps($userId)
    {
        /**
         * Check user id is not an integer
         */
        if (!is_int((int) $userId) && $userId != 0) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $arrSkill = self::where('user_id', (int) $userId)->orderBy('otp_trans_id','DESC')->where('is_otp_resent', '<' ,3)
            ->get();

        return ($arrSkill ?: false);
    }
    
    public static function getOtpsbyActive($userId)
    {
        /**
         * Check user id is not an integer
         */
        if (!is_int((int) $userId) && $userId != 0) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $arrSkill = self::where('user_id', (int) $userId)->where('is_otp_expired', 0)
            ->get();

        return ($arrSkill ?: false);
    }
    
    public static function selectOtpbyLastExpired($userId)
    {
        /**
         * Check user id is not an integer
         */
        if (!is_int((int) $userId) && $userId != 0) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $arrSkill = self::where('user_id', (int) $userId)
                ->where('is_otp_expired', 0)
                ->orderBy('otp_trans_id', 'DESC')
            ->first();

        return ($arrSkill ?: false);
    }
    
    public static function selectOtpbyLastExpiredByThirty($userId)
    {
        $date = new DateTime;
        $currentDate = $date->format('Y/m/d H:i:s');
        /**
         * Check user id is not an integer
         */
        if (!is_int((int) $userId) && $userId != 0) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $arrSkill = self::where('user_id', (int) $userId)
                ->whereRaw('TIMESTAMPDIFF(MINUTE, otp_exp_time, NOW()) > 30')
                ->where('is_otp_expired', 1)
                ->orderBy('otp_trans_id', 'DESC')
            ->first();

        return ($arrSkill ?: false);
    }
    
    
    



    /**
     * Get Otp
     *
     * @param  integer $userId
     *
     * @return mixed Array | Boolean false
     * @throws InvalidDataTypeExceptions
     */
    public static function getUserByOPT($otp,$user_id)
    {
        /**
         * Check user id is not an integer
         */
        if (!is_int((int) $otp) && $otp != 0) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

       /* $arrSkill = self::where('otp_id', (int) $otp)
            ->get();

*/
         $arrUser = self::select('otp_trans.*', 'users.f_name','users.l_name','users.m_name','users.email')
            ->leftjoin('users', 'otp_trans.user_id', '=', 'users.user_id')
            ->where('otp_trans.otp_no', (int) $otp)
            ->where('otp_trans.user_id', (int) $user_id)
            ->where('otp_trans.is_otp_expired', 0)
            ->first();



        return ($arrUser ?: false);
    }

    /**
     * Delete otp
     *
     * @param  integer $userId
     * @return mixed Array | Boolean false
     * @throws InvalidDataTypeExceptions
     */
    public static function deleteOtp($userId)
    {
        /**
         * Check user id is not an integer
         */
        if (!is_int((int) $userId) && $userId != 0) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $sklDelete = self::where('user_id', '=', $userId)->delete();

        return ($sklDelete ?: false);
    }
    
        /*
     * Save or update education details
     * @param $postArr, $id
     * return int
     */

    public static function updateOtp($arrOtp, $id)
    {
        //Check data is Array
        if (!is_array($arrOtp)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }
        
        //Check id is integer
        if (!is_int($id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        $res = self::where('otp_trans_id', $id)->update($arrOtp);
        return $res ?: false;
    }
    
    public static function updateOtpByExpiry($arrOtp, $user_id)
    {
        $date = new DateTime;
        $currentDate = $date->format('Y/m/d H:i:s');
        //Check data is Array
        if (!is_array($arrOtp)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }
        
        //Check id is integer
        if (!is_int($user_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
    //    dd($arrOtp)
        $res = self::where('user_id', $user_id)->limit(4)->orderBy('otp_trans_id', 'DESC')
                ->update($arrOtp);
        return $res ?: false;
    }
    
}


