<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;

class UserSocialmedia extends BaseModel
{
    /* The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users_socialmedia';

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
        'social_media',
        'social_media_link',
        'expire_date',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',       
    ]; 
    
    
    /**
     * store Document Type
     *
     * @param  integer $user_kyc_id
     * @return mixed Array | Boolean false
     * @throws InvalidDataTypeExceptions
     * @author  arvind soni
     */
    public static function storeData($inputData,$id='') {
        try {
            $data = self::updateOrCreate(['id' => (int) $id],$inputData);
            return ($data ?: false);
        } catch (\Exception $e) {
            $data['errors'] = $e->getMessage();
        }
    }
    
    
   
    /**
     * Get Document Type
     *
     * @param  integer $user_kyc_id
     * @return mixed Array | Boolean false
     * @throws InvalidDataTypeExceptions
     * @author  arvind soni
     */
    public static function getData($user_kyc_id=''){
        $result=self::where('user_kyc_id','=',$user_kyc_id)->get();
        return ($result?:false);
    }
    
    /**
     * Delete Data
     *
     * @param  integer $user_kyc_id
     * @return mixed Array | Boolean false
     * @throws InvalidDataTypeExceptions
     * @author  arvind soni
     */
    public static function deleteData($user_kyc_id)
    {
        /**
         * Check user id is not an integer
         */
        if (!is_int((int) $user_kyc_id) && $user_kyc_id != 0) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $sklDelete = self::where('user_kyc_id', '=', $user_kyc_id)->delete();

        return ($sklDelete ?: false);
    }
}