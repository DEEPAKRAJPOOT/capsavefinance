<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\RcuRcuLog;
use Auth;

class RcuDocument extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'rcu_doc';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'rcu_doc_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'app_id',
        'agency_id',
        'from_id',
        'to_id',
        'doc_id',
        'rcu_status_id',
        'rcu_status_updated_by',
        'rcu_status_updatetime',
        'rcu_comment',
        'cm_rcu_status_id',
        'cm_status_updatetime',
        'cm_status_updated_by',
        'is_active',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];
    
    
    public static function getRcuAgencies($appId, $docId)
    {
        return RcuDocument::with('status')->where('app_id', $appId)
                ->where('doc_id', $docId)
                ->get();
        
    }
    
    public function agency(){
        return $this->belongsTo('App\Inv\Repositories\Models\Agency','agency_id','agency_id');
    }

    public function user(){
        return $this->belongsTo('App\Inv\Repositories\Models\User','to_id','user_id');
    }
    
    public function status(){
        return $this->belongsTo('App\Inv\Repositories\Models\Master\Status', 'rcu_status_id', 'id');
    }
    
    public static function changeAgentRcuStatus($data){
        return RcuDocument::where('rcu_doc_id',$data->rcu_doc_id)->update([
            'rcu_status_id'=>$data->status,
            'rcu_status_updated_by'=>Auth::user()->user_id,
            'rcu_status_updatetime'=>\Carbon\Carbon::now()
            ]);
    }

    public static function changeCmRcuStatus($data){
        return RcuDocument::where('rcu_doc_id',$data->rcu_doc_id)->update([
            'rcu_status_id'=>$data->status,
            'rcu_status_updated_by'=>Auth::user()->user_id,
            'rcu_status_updatetime'=>\Carbon\Carbon::now()
            ]);
    }
}
