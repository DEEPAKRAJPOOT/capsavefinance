<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;

class Userpersonal extends BaseModel
{
    /* The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_kyc_personal';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'user_personal_id';

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
        'user_kyc_id',
        'user_id',
        'title',
        'f_name',
        'm_name',
        'l_name',
        'gender',
        'date_of_birth',
        'birth_country_id',
        'birth_state_id',
        'birth_city_id',
        'father_name',
        'mother_f_name',
        'mother_m_name',
        'reg_no',
        'reg_place',
        'f_nationality_id',
        'sec_nationality_id',
        'residence_status',
        'family_status',
        'guardian_name',
        'legal_maturity_date',
        'educational_level',
        'is_residency_card',
        'political_position',
        'political_position_dec',
        'related_political_position',
        'related_political_position_dec',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        
    ];

    
    /**
     * Save Otp
     *
     * @param  array $arrData
     *
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions
     */
    public static function saveUserPersonal($arrData = [])
    {

       //Check data is Array
        if (!is_array($arrData)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        //Check data is not blank
        if (empty($arrData)) {
            throw new BlankDataExceptions(trans('error_message.data_not_found'));
        }
        
        //  echo "-----<pre>";
        //print_r($arrData);

        /**
         * Create Personal Information
         */
        $objotp = self::create($arrData);

        return ($objotp->id ?: false);
    }
    
    /**
     * storeData
     * @param
     * @return array
     * @since 0.1
     * @author Arvind soni
     */
    public static function storeData($inputData,$id='') {
        try {
            $data = self::updateOrCreate(['user_personal_id' => (int) $id],$inputData);
            return ($data ?: false);
        } catch (\Exception $e) {
            $data['errors'] = $e->getMessage();
        }
    }
    
    
    /*
     * Get Personal infomation
     * @param int user_id
     * @return array
     *@author  arvind soni
    */
    public static function getData($user_id=''){
        $result=self::where('user_id','=',$user_id)->first();
        return ($result?:false);
    }
    
    //
    
    /*
     * Get Kyc Personal infomation
     * @param int user_kyc_id
     * @return array
     *@author  arvind soni
    */
    public static function getKycPersonalData($user_kyc_id=''){
        $result=self::where('user_kyc_id','=',$user_kyc_id)->first();
        return ($result?:false);
    }

    /**
     * Get Otp
     *
     * @param  integer $userId
     *
     * @return mixed Array | Boolean false
     * @throws InvalidDataTypeExceptions
     */
    public static function getUserPersonalData($userId)
    {
        /**
         * Check user id is not an integer
         */
        if (!is_int((int) $userId) && $userId != 0) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $arrData = self::where('user_id', (int) $userId)
            ->first();

        return ($arrData ?: false);
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
}