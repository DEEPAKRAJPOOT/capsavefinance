<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\BusinessAddress;
use App\Inv\Repositories\Models\BizPanGstApi;
use App\Inv\Repositories\Models\BizPanGst;
use App\Inv\Repositories\Models\Application;
use Carbon\Carbon;

class Business extends BaseModel
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'biz';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'biz_id';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    //protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'biz_entity_name',
        'date_of_in_corp',
        'entity_type_id',
        'turnover_amt',
        'nature_of_biz',
        'tenor_days',
        'biz_constitution',
        'biz_segment',
        'is_pan_verified',
        'is_gst_verified',
        'panno_pan_gst_id',
        'gstno_pan_gst_id',
        'org_id',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    public static function creates($attributes,$userId){
        $business = Business::create([
        'user_id'=>$userId,
        'biz_entity_name'=>$attributes['biz_entity_name'],
        'date_of_in_corp'=>Carbon::createFromFormat('d/m/Y', $attributes['incorporation_date'])->format('Y-m-d'),
        'entity_type_id'=>$attributes['entity_type_id'],
        'nature_of_biz'=>$attributes['biz_type_id'],
        'turnover_amt'=>str_replace(',', '', $attributes['biz_turnover']),
        'tenor_days'=>$attributes['tenor_days'],
        'biz_constitution'=>$attributes['biz_constitution'],
        'biz_segment'=>$attributes['segment'],
        'org_id'=>1,
        'created_by'=>$userId,
        ]);

        $bpga = BizPanGstApi::create([
                'file_name'=>'file name goes here',
                'status'=>1,
                'created_by'=>$userId
            ]);

        //entry for parent PAN
        $bpg = BizPanGst::create([
                'user_id'=>$userId,
                'biz_id'=>$business->biz_id,
                'type'=>1,
                'pan_gst_hash'=>$attributes['biz_pan_number'],
                'status'=>1,
                'parent_pan_gst_id'=>0,
                'biz_pan_gst_api_id'=>$bpga->biz_pan_gst_api_id,
                'cin'=>$attributes['biz_cin'],
                'created_by'=>$userId
            ]);

        //entry for parent GST
        BizPanGst::create([
                'user_id'=>$userId,
                'biz_id'=>$business->biz_id,
                'type'=>2,
                'pan_gst_hash'=>$attributes['biz_gst_number'],
                'status'=>1,
                'parent_pan_gst_id'=>0,
                'biz_pan_gst_api_id'=>0,
                'created_by'=>$userId
            ]);

        //entry for all GST against the PAN
        $pan_api_res = explode(',', rtrim($attributes['pan_api_res'],','));
        $data = [];
        foreach ($pan_api_res as $key=>$value) {
            $data[$key]['user_id']=$userId;
            $data[$key]['biz_id']=$business->biz_id;
            $data[$key]['type']=2;
            $data[$key]['pan_gst_hash']=$value;
            $data[$key]['status']=1;
            $data[$key]['parent_pan_gst_id']=$bpg->biz_pan_gst_id;
            $data[$key]['created_by']=$userId;
            $data[$key]['biz_pan_gst_api_id']=0;
        }
        BizPanGst::insert($data);

        // insert into rta_app table
        $app = Application::create([
                'user_id'=>$userId,
                'biz_id'=>$business->biz_id,
                'loan_amt'=>str_replace(',', '', $attributes['loan_amount']),
                'created_by'=>$userId
            ]);

        Business::where('biz_id', $business->biz_id)->update([
            'panno_pan_gst_id'=>$bpg->biz_pan_gst_id,
            'is_pan_verified'=>1,
            'gstno_pan_gst_id'=>0,
            'is_gst_verified'=>1,
            ]);

        //insert address into rta_biz_addr
        $address_data=[];
        array_push($address_data, array('biz_id'=>$business->biz_id, 'addr_1'=> $attributes['biz_address'],'city_name'=>$attributes['biz_city'],'state_id'=>$attributes['biz_state'],'pin_code'=>$attributes['biz_pin'],'address_type'=>0,'created_by'=>$userId,'rcu_status'=>0));
        for($i=0; $i <=3 ; $i++) { 
            $temp = array('biz_id'=>$business->biz_id, 'addr_1'=> $attributes['biz_other_address'][$i],'city_name'=>$attributes['biz_other_city'][$i],'state_id'=>$attributes['biz_other_state'][$i],'pin_code'=>$attributes['biz_other_pin'][$i],'address_type'=>($i+1),'created_by'=>$userId,'rcu_status'=>0);
            array_push($address_data, $temp);
        }
        BusinessAddress::insert($address_data);

        return ['biz_id'=>$business->biz_id,'app_id'=>$app->app_id];
    }
    
     /**
     * Get all users
     *
     * @return array
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     * Since 0.1
     */
    public static function getAllBusinesses()
    {
        $roleData = User::getBackendUser(\Auth::user()->user_id);
        $result = self::distinct()->select('users.user_id','users.f_name','users.l_name','users.email','users.mobile_no','users.created_at', 'anchor_user.anchor_id as UserAnchorId','anchor_user.user_type as AnchUserType','lead_assign.to_id')
                 ->leftJoin('lead_assign',  'lead_assign.assigned_user_id','users.user_id')
                 ->leftJoin('anchor_user',  'anchor_user.user_id','users.user_id') 
                 ->where('users.user_type', 1);
        if ($roleData[0]->is_superadmin != 1) {
            $result->where('lead_assign.to_id', \Auth::user()->user_id);
            $result->where('lead_assign.is_owner', 1);
        }
        //$result->groupBy('users.user_id');
        $result = $result->orderBy('users.user_id', 'desc');
                 
        return ($result ? $result : '');
    }
    
    
    public static function getApplicationById($bizId){
        return Business::where('biz.biz_id', $bizId)->first();
        /*$bizData = self::select('app.app_id', 'app.loan_amt','biz.biz_entity_name','biz.date_of_in_corp','biz.entity_type_id','biz.turnover_amt','biz.nature_of_biz','biz.org_id','bpg1.cin','bpg1.pan_gst_hash')
                ->join('app', 'biz.biz_id', '=', 'app.biz_id')
                ->join('biz_pan_gst as bpg1', 'bpg1.biz_pan_gst_id', '=', 'biz.panno_pan_gst_id')
                //->join('biz_pan_gst as bpg2', 'bpg2.biz_pan_gst_id', '=', 'biz.gstno_pan_gst_id')
                ->where('biz.biz_id', $bizId)
                ->get();*/
    }

    public function app(){
        return $this->belongsTo('App\Inv\Repositories\Models\Application','biz_id','biz_id');
    }

    public function address(){
        return $this->hasMany('App\Inv\Repositories\Models\BusinessAddress','biz_id','biz_id')->where('biz_owner_id', null);
    }

    public function gsts(){
        return $this->hasMany('App\Inv\Repositories\Models\BizPanGst','biz_id','biz_id')->where(['type'=>2, 'biz_owner_id'=>null])->where('parent_pan_gst_id','<>',0);
    }

    public function pan(){
        return $this->belongsTo('App\Inv\Repositories\Models\BizPanGst','biz_id','biz_id')->where(['type'=>1, 'biz_owner_id'=>null]);
    }

    public function gst(){
        return $this->belongsTo('App\Inv\Repositories\Models\BizPanGst','biz_id','biz_id')->where(['type'=>2, 'biz_owner_id'=>null, 'parent_pan_gst_id'=>0]);
    }

    public static function getCompanyDataByBizId($biz_id)
    {
        $arrData = self::select('biz.biz_entity_name','biz_pan_gst.pan_gst_hash')
        ->join('biz_pan_gst', 'biz_pan_gst.biz_pan_gst_id', '=', 'biz.panno_pan_gst_id')
        ->where('biz.biz_id', $biz_id)
        ->get();
        return $arrData;
    }


    public static function updateCompanyDetail($attributes, $bizId, $userId){
        $business = Business::where('biz_id', $bizId)->first();

        //update business table
        $business->update([
        'biz_entity_name'=>$attributes['biz_entity_name'],
        'date_of_in_corp'=>Carbon::createFromFormat('d/m/Y', $attributes['incorporation_date'])->format('Y-m-d'),
        'entity_type_id'=>$attributes['entity_type_id'],
        'nature_of_biz'=>$attributes['biz_type_id'],
        'turnover_amt'=>str_replace(',', '', $attributes['biz_turnover']),
        'tenor_days'=>$attributes['tenor_days'],
        'biz_constitution'=>$attributes['biz_constitution'],
        'biz_segment'=>$attributes['segment'],
        'org_id'=>1,
        'updated_by'=>$userId,
        ]);

        if(!empty($attributes->pan_api_res)){
            BizPanGst::where(['biz_id'=>$bizId,'biz_owner_id'=>null])->delete();
            $bpga = BizPanGstApi::create([
                    'file_name'=>'file name goes here',
                    'status'=>1,
                    'created_by'=>$userId
                ]);

            //entry for parent PAN
            $bpg = BizPanGst::create([
                    'user_id'=>$business->user_id,
                    'biz_id'=>$bizId,
                    'type'=>1,
                    'pan_gst_hash'=>$attributes['biz_pan_number'],
                    'status'=>1,
                    'parent_pan_gst_id'=>0,
                    'biz_pan_gst_api_id'=>$bpga->biz_pan_gst_api_id,
                    'cin'=>$attributes['biz_cin'],
                    'updated_by'=>$userId
                ]);

            //entry for parent GST
            BizPanGst::create([
                    'user_id'=>$business->user_id,
                    'biz_id'=>$bizId,
                    'type'=>2,
                    'pan_gst_hash'=>$attributes['biz_gst_number'],
                    'status'=>1,
                    'parent_pan_gst_id'=>0,
                    'biz_pan_gst_api_id'=>0,
                    'created_by'=>$userId
                ]);

            //entry for all GST against the PAN
            $pan_api_res = explode(',', rtrim($attributes['pan_api_res'],','));
            $data = [];
            foreach ($pan_api_res as $key=>$value) {
                $data[$key]['user_id']=$business->user_id;
                $data[$key]['biz_id']=$bizId;
                $data[$key]['type']=2;
                $data[$key]['pan_gst_hash']=$value;
                $data[$key]['status']=1;
                $data[$key]['parent_pan_gst_id']=$bpg->biz_pan_gst_id;
                $data[$key]['created_by']=$userId;
                $data[$key]['biz_pan_gst_api_id']=0;
            }
            BizPanGst::insert($data);

            $business->update([
                'panno_pan_gst_id'=>$bpg->biz_pan_gst_id,
                'is_pan_verified'=>1,
                'gstno_pan_gst_id'=>0,
                'is_gst_verified'=>1,
                ]);
        }else if(empty($attributes->pan_api_res) && !empty($attributes->biz_cin)){
            //update for parent GST
            BizPanGst::where(['type'=>2,'biz_id'=>$bizId, 'parent_pan_gst_id'=>0, 'biz_owner_id'=>null])->update([
                    'pan_gst_hash'=>$attributes['biz_gst_number'],
                    'updated_by'=>$userId
                ]);

            //update for CIN
            BizPanGst::where(['type'=>1,'biz_id'=>$bizId, 'parent_pan_gst_id'=>0, 'biz_owner_id'=>null])->update([
                    'cin'=>$attributes['biz_cin'],
                    'updated_by'=>$userId
                ]);
        }

        // update into rta_app table
        $app_id = Application::where('biz_id',$bizId)->update([
                'loan_amt'=>str_replace(',', '', $attributes['loan_amount']),
                'updated_by'=>$userId
            ]);


        //get id from address and then update address into rta_biz_addr
        $biz_addr_ids = BusinessAddress::where('biz_id',$bizId)->pluck('biz_addr_id');
        $address_data=[];
        BusinessAddress::where('biz_addr_id',$biz_addr_ids[0])->update(
            array('addr_1'=> $attributes['biz_address'],'city_name'=>$attributes['biz_city'],'state_id'=>$attributes['biz_state'],'pin_code'=>$attributes['biz_pin'],'updated_by'=>$userId)
            );
        
        for ($i=0; $i <=3 ; $i++) { 
            $temp = array('addr_1'=> $attributes['biz_other_address'][$i],'city_name'=>$attributes['biz_other_city'][$i],'state_id'=>$attributes['biz_other_state'][$i],'pin_code'=>$attributes['biz_other_pin'][$i],'created_by'=>$userId);
            BusinessAddress::where('biz_addr_id',$biz_addr_ids[$i+1])->update($temp);
        }

        return true;
    }

    public function registeredAddress(){
        return $this->hasOne('App\Inv\Repositories\Models\BusinessAddress','biz_id','biz_id')->where(['biz_owner_id'=>null, 'address_type'=>0]);
    }

    public function communicationAddress(){
        return $this->hasOne('App\Inv\Repositories\Models\BusinessAddress','biz_id','biz_id')->where(['biz_owner_id'=>null, 'address_type'=>1]);
    }


    public function factoryAddress(){
        return $this->hasOne('App\Inv\Repositories\Models\BusinessAddress','biz_id','biz_id')->where(['biz_owner_id'=>null, 'address_type'=>4]);
    }

    

    public static function getEntityByBizId($biz_id)
    {
        $arrData = self::select('mst_biz_entity.entity_name','mst_biz_constitution.name', 'users.email', 'users.mobile_no')
        ->leftjoin('mst_biz_entity', 'mst_biz_entity.id', '=', 'biz.entity_type_id')
        ->leftjoin('mst_biz_constitution', 'mst_biz_constitution.id', '=', 'biz.biz_constitution')
        ->leftjoin('users', 'users.user_id', '=', 'biz.user_id')
        ->where('biz.biz_id', $biz_id)
        ->first();
        return $arrData;
    }

}
