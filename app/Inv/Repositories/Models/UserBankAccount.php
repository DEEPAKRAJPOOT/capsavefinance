<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class UserBankAccount extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_bank_account';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'acc_id';

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
        'user_id',
        'acc_name',
        'bank_id',
        'is_default',
        'acc_no',
        'ifsc_code',
        'branch_name',
        'is_active',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    /**
     * Save bank account
     * 
     * @param type $attributes array
     * @param type $id int
     * @return type mixed
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions 
     */
    public static function saveBankAccount($attributes, $id = null)
    {
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        if (empty($attributes)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        $query = self::updateOrCreate(['acc_id' => $id], $attributes);
        return $query ? $query->acc_id : $id;
    }

    /**
     * bank account list
     * 
     * @return type mixed
     */
    public static function getBankAccountList()
    {
        $res = self::select('user_bank_account.*', 'mb.bank_name')
                ->join('mst_bank as mb', 'user_bank_account.bank_id', '=', 'mb.id');
        return ($res ?: false);
    }

    /**
     * update bank account
     * 
     * @param type $attributes Array
     * @param type $where Array
     * @return type mixed
     */
    public static function updateBankAccount($attributes, $where = [])
    {


        $result = \DB::table('user_bank_account');
        if (!empty($where)) {
            $result = $result->where($where);
        }
        $result = $result->update($attributes);
        return $result ?: false;
    }

}
