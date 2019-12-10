<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\FiRcuLog;
use Auth;

class FiAddress extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'fi_addr';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'fi_addr_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'agency_id',
        'from_id',
        'to_id',
        'biz_addr_id',
        'fi_status',
        'fi_status_updated_by',
        'fi_status_updatetime',
        'fi_comment',
        'cm_status_updatetime',
        'cm_status_updated_by',
        'is_active',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    public function agency(){
        return $this->belongsTo('App\Inv\Repositories\Models\Agency','agency_id','agency_id');
    }

    public function user(){
        return $this->belongsTo('App\Inv\Repositories\Models\User','to_id','user_id');
    }

    public static function insertFiAddress($data){
        $addr_ids = explode('#', trim($data['address_ids'], '#'));
        $customLogArr = [];
        $customAddArr = [];
        foreach ($addr_ids as $key=>$value) {
            $customLogArr[$key]['whom_id']=$value;
            $customLogArr[$key]['fi_rcu_type']=1;
            $customLogArr[$key]['fi_rcu_status']=2;
            $customLogArr[$key]['fi_rcu_comment']=$data['comment'];
            $customLogArr[$key]['created_by']=Auth::user()->user_id;

            $customAddArr[$key]['agency_id']=$data['agency_id'];
            $customAddArr[$key]['from_id']=Auth::user()->user_id;
            $customAddArr[$key]['to_id']=$data['to_id'];
            $customAddArr[$key]['biz_addr_id']=$value;
            $customAddArr[$key]['fi_comment']=$data['comment'];
            $customAddArr[$key]['is_active']=1;
            $customAddArr[$key]['created_by']=Auth::user()->user_id;
        }
        $q = FiRcuLog::insert($customLogArr);
        $fiAddress = FiAddress::insert($customAddArr);
        return $fiAddress;
    }

}
