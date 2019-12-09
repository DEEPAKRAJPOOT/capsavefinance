<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;
use App\Inv\Repositories\Factory\Models\BaseModel;

class UserBussinessAddress extends BaseModel {  //

    /* The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_kyc_buss_addr';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'buss_addr_id';

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
    'user_kyc_id',
    'user_id',
    'buss_country_id',
    'buss_city_id',
    'buss_region',
    'buss_building',
    'buss_floor',
    'buss_street',
    'buss_postal_code',
    'buss_po_box_no',
    'buss_email',
    'buss_telephone_no',
    'buss_mobile_no',
    'buss_fax_no',
    'created_at',
    'updated_at'
    ];

    /**
     * storeData
     * @param
     * @return array
     * @since 0.1
     * @author Arvind soni
     */
    public static function storeData($inputData,$id='') {//dd($inputData);
        try {
            $data = self::updateOrCreate(['buss_addr_id' => (int) $id],$inputData);
            return ($data ?: false);
        } catch (\Exception $e) {
            $data['errors'] = $e->getMessage();
        }
    }
    
    /*
     * Get Residential infomation
     * @param int user_id
     * @return array
     *@author  arvind soni
    */
    public static function getData($user_kyc_id=''){
        $result=self::where('user_kyc_id','=',$user_kyc_id)->first();
        return ($result?:false);
    }

}
