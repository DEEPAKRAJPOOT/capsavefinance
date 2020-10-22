<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class UserNach extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users_nach';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'users_nach_id';

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
        'parent_users_nach_id',
        'user_id',
        'anchor_id',
        'bank_account_id',
        'umrn',
        'nach_date',
        'sponsor_bank_code',
        'utility_code',
        'here_by_authorize',
        'debit_tick',
        'acc_name',
        'acc_no',
        'ifsc_code',
        'micr',
        'branch_name',
        'amount',
        'frequency',
        'debit_type',
        'phone_no',
        'email_id',
        'reference_1',
        'reference_2',
        'period_from',
        'period_to',
        'period_until_cancelled',
        'uploaded_file_id',
        'request_id',
        'is_active',
        'request_for',
        'nach_status_log_id',
        'nach_status',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];


    public function  user()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\User','user_id','user_id');
    }

    public function  lms_user()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\LmsUser','user_id','user_id');
    }
    
    public function  user_bank()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\UserBankAccount','bank_account_id','bank_account_id');
    }

    public function  child_nach()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\UserNach','users_nach_id','parent_users_nach_id');
    }
    
    /**
     * Save Nach
     * 
     * @param type $attributes array
     * @param type $id int
     * @return type mixed
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions 
     */
    public static function saveNach($attributes, $users_nach_id = null)
    {
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        if (empty($attributes)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        $query = self::updateOrCreate(['users_nach_id' => $users_nach_id], $attributes);
        return $query ? $query->users_nach_id : $users_nach_id;
    }

    /**
     * Nach list
     * 
     * @return type mixed
     */
    public static function getNach()
    {
        $res = self::select('*')->get();
        return ($res ?: false);
    }

    /**
     * Update Nach
     * 
     * @param type $attributes
     * @param type $users_nach_id
     * @return type
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions
     */
    public static function updateNach($attributes, $users_nach_id)
    {
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        if (empty($attributes)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        $result = self::find((int) $users_nach_id)->update($attributes);
        
        return $result ?: false;
    }

    /**
     * get Nach data
     * 
     * @param type $whereCond
     * @return type
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions
     */
    public static function getNachData($whereCond)
    {
        if (!is_array($whereCond)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }
        if (empty($whereCond)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        $res = self::where($whereCond)->first();
        return $res ?: false;
    }
    
    /**
     * get Nach data In Users_nach_id
     * 
     * @param type $whereCond
     * @return type
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions
     */
    public static function getNachDataInNachId($nachIds)
    {
        if (!is_array($nachIds)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }
        if (empty($nachIds)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        $res = self::whereIn('users_nach_id',$nachIds)
                ->with('user')
                ->with('lms_user')
                ->with('user_bank.bank')
                ->get();
        return $res ?: false;
    }
    
    /**
     * Update Nach by condition
     * 
     * @param arr $whereCond
     * @param arr $whereCond
     * @return type
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions
     */
    public static function updateNachByUserId($attributes, $whereCond)
    { //dd('$whereCond--', $whereCond);
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }
        
        if (!is_array($whereCond)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }
        
        if (empty($attributes)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        $result = self::where($whereCond)->update($attributes);
        return $result ? $result : false;
    }

}

