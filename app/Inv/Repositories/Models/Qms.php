<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;
use DB;

class Qms extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'qms_req';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'qms_req_id';

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
        'app_id',
        'assign_role_id',
        'file_id',
        'qms_cmnt',
        'created_by',
        'created_at'
    ];


    public static function saveQuery($attr, $id = null)
    {
        if (empty($attr)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        $result = self::updateOrCreate($attr, ['qms_req_id' => $id]);
        return $result ?: false;
    }

   public static function showQueryList($app_id)
    {
        $appNote = self::select('qms_req.*', 'users.f_name', 'users.m_name', 'users.l_name')
                ->join('users', 'users.user_id', '=', 'qms_req.created_by')
                ->where('qms_req.app_id', $app_id)
                ->orderBy('qms_req_id', 'DESC')
                ->get();
        return $appNote ?: false;
    }


    public static function getQueryData($id)
    {
        $arrData = self::where('qms_req_id','=',$id)->with('userFile')->first();
        return $arrData ?: false;
    }


    public function userFile()
   {
       return $this->hasMany('App\Inv\Repositories\Models\UserFile', 'file_id', 'file_id');
   }
   
 


}
