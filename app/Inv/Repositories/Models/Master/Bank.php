<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\User;

class Bank extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_bank';

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
        'bank_name',
        'perfios_bank_id',
        'is_active',
    ];

    /**
     * get Bank list
     * 
     * @param type $where array
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public static function getBankList()
    {
        $res = self::where('is_active', '1')->pluck('bank_name', 'id');
        return $res ?: false;
    }

    public static function saveBank($attributes, $id = null)
    {
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        if (empty($attributes)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        if (is_null($id)) {
            return self::create($attributes);
        } else {
            return self::where('id', $id)->update($attributes);
        }
    }


    /**
     * Check Bank name
     * 
     * @param type $where array
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public static function checkBankName($bankName, $banktId=null)
    {
        $query = self::select('id')
                ->where('bank_name', $bankName);
        if (!is_null($banktId)) {
            $query->where('id', '!=', $banktId);
        }
        $res = $query->get();        
        return $res ?: [];
    }    


}
