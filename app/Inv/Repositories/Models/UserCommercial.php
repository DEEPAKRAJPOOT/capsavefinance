<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;

class UserCommercial extends Model {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'user_kyc_propr';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'user_kyc_propr_id';

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
    
    protected $fillable = [
        'user_kyc_id',
        'user_id',
        'comm_name',
        'date_of_establish',
        'country_activity',
        'country_establish_id',
        'comm_reg_no',
        'comm_reg_place',
        'comm_country_id',
        'country_activity',
        'syndicate_no',
        'taxation_no',
        'taxation_id',
        'annual_turnover',
        'main_suppliers',
        'main_clients',
        'authorized_signatory',
        'fax_no',
        'is_hold_mail',
        'mailing_address',
        'relation_exchange_company',
        'concerned_party',
        'details_of_company',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];

    //protected $guarded=[];
    //protected $fillable=[];

    /**
     * storeData
     * @param
     * @return array
     * @since 0.1
     * @author Arvind soni
     */
    public static function storeData($inputData, $id = '') {
        try {
            $data = self::updateOrCreate(['user_kyc_propr_id' => (int) $id], $inputData);
            return ($data ? : false);
        } catch (\Exception $e) {
            $data['errors'] = $e->getMessage();
        }
    }

    /*
     * Get Professional infomation
     * @param int user_id
     * @return array
     * @author  arvind soni
     */

    public static function getData($user_kyc_id = '') {
        $result = self::where('user_kyc_id', '=', $user_kyc_id)->first();
        return ($result? : false);
    }

}
