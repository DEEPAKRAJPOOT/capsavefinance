<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfessional extends Model {  //

    /* The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_kyc_prof';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'user_kyc_prof_id';

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
    'prof_status',
    'other_prof_status',    
    'prof_detail',
    'position_title',
    'date_employment',
    'last_monthly_salary',   
    'created_at',
    'created_by',
    'updated_at',
    'updated_by'];
    
    /**
     * storeData
     * @param
     * @return array
     * @since 0.1
     * @author Arvind soni
     */
    public static function storeData($inputData,$id='') {
        try {
            $data = self::updateOrCreate(['user_kyc_prof_id' => (int) $id],$inputData);
            return ($data ?: false);
        } catch (\Exception $e) {
            $data['errors'] = $e->getMessage();
        }
    }
    
    /*
     * Get Professional infomation
     * @param int user_id
     * @return array
     *@author  arvind soni
    */
    public static function getData($user_kyc_id=''){
        $result=self::where('user_kyc_id','=',$user_kyc_id)->first();
        return ($result?:false);
    }
}