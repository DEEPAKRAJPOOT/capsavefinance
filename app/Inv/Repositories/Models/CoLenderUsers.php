<?php

namespace App\Inv\Repositories\Models;

use Carbon\Carbon;
use DateTime;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class CoLenderUsers extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'co_lenders_user';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'co_lender_id';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    //protected $dates = ['deleted_at'];

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
        'comp_name',
        'comp_email',
        'comp_addr',
        'comp_state',
        'comp_city',
        'comp_zip',
        'comp_phone',
        'doc_name',
        'gst',
        'pan_no',
        'is_active',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    /**
     * save co lender Users
     * 
     * @param Array $attributes
     * @return mixed
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions
     */
    public static function saveColenderUsers($attributes , $id = null)
    {
        //Check data is Array
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.send_array'));
        }

        //Check data is not blank
        if (empty($attributes)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }

        /**
         * Create anchor
         */
      //  dd($id , $attributes);
        $res = self::UpdateOrcreate(['co_lender_id'=> $id] , $attributes );
        return ($res->co_lender_id ?: false);
    }

    /**
     * get colender list
     * 
     * @return mixed
     */
    public static function getColenderList()
    {
        $res = self::select('co_lenders_user.*', 'u.f_name', 'u.biz_name', 'u.email')
                ->join('users as u', 'co_lenders_user.co_lender_id', '=', 'u.co_lender_id');
        return $res ?: false;
    }

    /**
     * get colender data
     * 
     * @param array $where
     * @return mixed
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions
     */
    public static function getCoLenderData($where)
    {
        //Check data is Array
        if (!is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.send_array'));
        }

        //Check data is not blank
        if (empty($where)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }

        return self::where($where)->select('co_lenders_user.*' ,'u.f_name', 'u.biz_name', 'u.email')
                ->join('users as u', 'co_lenders_user.co_lender_id', '=', 'u.co_lender_id')
                ->get();
    }

    public function user(){
        return $this->belongsTo('App\Inv\Repositories\Models\User', 'co_lender_id', 'co_lender_id');
    }

}
