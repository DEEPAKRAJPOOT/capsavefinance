<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\FiStatusLog;
use App\Inv\Repositories\Models\FiFileLog;
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
        'fi_status_id',
        'fi_status_updated_by',
        'fi_status_updatetime',
        'fi_comment',
        'cm_fi_status_id',
        'cm_status_updatetime',
        'cm_status_updated_by',
        'file_id',
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
        //Update is_active to 0 in fi_address
        FiAddress::whereIn('biz_addr_id',$addr_ids)->update(['is_active'=>0]);
        foreach ($addr_ids as $key=>$value) {
            $customAddArr[$key]['agency_id']=$data['agency_id'];
            $customAddArr[$key]['from_id']=Auth::user()->user_id;
            $customAddArr[$key]['to_id']=$data['to_id'];
            $customAddArr[$key]['biz_addr_id']=$value;
            $customAddArr[$key]['fi_status_id']=2;
            $customAddArr[$key]['fi_status_updated_by']=0;
            $customAddArr[$key]['fi_comment']=$data['comment'];
            $customAddArr[$key]['cm_fi_status_id']=1;
            $customAddArr[$key]['file_id']=0;
            $customAddArr[$key]['is_active']=1;
            $customAddArr[$key]['created_at']=\Carbon\Carbon::now();
            $customAddArr[$key]['created_by']=Auth::user()->user_id;
            $fiAddress = FiAddress::create($customAddArr[$key]);

            $customLogArr[$key]['fi_addr_id']=$fiAddress->fi_addr_id;
            $customLogArr[$key]['fi_status_id']=2;
            $customLogArr[$key]['fi_comment']=$data['comment'];
            $customLogArr[$key]['created_at']=\Carbon\Carbon::now();
            $customLogArr[$key]['created_by']=Auth::user()->user_id;
        }

        FiStatusLog::insert($customLogArr);
        return true;
    }

    public function status(){
        return $this->belongsTo('App\Inv\Repositories\Models\Master\Status', 'fi_status_id', 'id');
    }

    public function userFile(){
        return $this->belongsTo('App\Inv\Repositories\Models\UserFile', 'file_id', 'file_id');
    }

    public static function changeAgentFiStatus($data){
        FiStatusLog::insert(['fi_addr_id'=>$data->fi_addr_id,'fi_status_id'=>$data->status,'created_at'=>\Carbon\Carbon::now()]);
        return FiAddress::where('fi_addr_id',$data->fi_addr_id)->update([
            'fi_status_id'=>$data->status,
            'fi_status_updated_by'=>Auth::user()->user_id,
            'fi_status_updatetime'=>\Carbon\Carbon::now()
            ]);
    }

    public static function changeCmFiStatus($data){
        $last_active_addr = FiAddress::where(['biz_addr_id'=>$data->addr_id, 'is_active'=>1])->first();
        if($last_active_addr){
            FiStatusLog::insert(['fi_addr_id'=>$last_active_addr->fi_addr_id,'fi_status_id'=>$data->status,'created_at'=>\Carbon\Carbon::now()]);
            return FiAddress::where('fi_addr_id',$last_active_addr->fi_addr_id)->update([
                'cm_fi_status_id'=>$data->status,
                'cm_status_updated_by'=>Auth::user()->user_id,
                'cm_status_updatetime'=>\Carbon\Carbon::now()
                ]);
        }else{
            //take rest
        }
    }

    public static function updateFiFile($data, $fiAddrId){
        $file_log =  FiFileLog::create([
            'fi_addr_id'=>$fiAddrId,
            'file_id'=>$data->file_id,
            'created_by'=>Auth::user()->user_id,
            'created_at'=>\Carbon\Carbon::now()
            ]);

        return FiAddress::where('fi_addr_id',$fiAddrId)->update([
            'file_id'=>$data->file_id
            ]);
    }

    public function updateAddressStatus($biz_addr_id){
        return BusinessAddress::where('biz_addr_id', $biz_addr_id)->update();
    }

}
