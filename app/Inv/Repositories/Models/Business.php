<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\BusinessAddress;
use App\Inv\Repositories\Models\BizPanGstApi;
use App\Inv\Repositories\Models\BizPanGst;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\LmsUser;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\Master\Segment;
use App\Inv\Repositories\Models\Master\Industry;
use Carbon\Carbon;
use Auth;

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
        'is_gst_manual',
        'panno_pan_gst_id',
        'gstno_pan_gst_id',
        'share_holding_date',
        'busi_pan_comm_date',
        'org_id',        
        'msme_type',
        'msme_no',
        'label_1',
        'label_2',
        'label_3',
        'email',
        'mobile',
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
        'turnover_amt'=>(isset($attributes['biz_turnover']) && $attributes['biz_turnover'])? str_replace(',', '', $attributes['biz_turnover']): 0,
       // 'tenor_days'=>$attributes['tenor_days'],
        'biz_constitution'=>$attributes['biz_constitution'],
        'biz_segment'=>$attributes['segment'],
        'share_holding_date'=> isset($attributes['share_holding_date']) ? Carbon::createFromFormat('d/m/Y', $attributes['share_holding_date'])->format('Y-m-d') : null,
        'busi_pan_comm_date'=> isset($attributes['busi_pan_comm_date']) ? Carbon::createFromFormat('d/m/Y', $attributes['busi_pan_comm_date'])->format('Y-m-d') : null,
        'org_id'=>1,
        'created_by'=>Auth::user()->user_id,
        'is_gst_manual'=>$attributes['is_gst_manual'],
        'msme_type' => $attributes['msme_type'],
        'msme_no' => $attributes['msme_no'],
        'label_1' => $attributes['label']['1'] ?? '',
        'label_2' => $attributes['label']['2'] ?? '',
        'label_3' => $attributes['label']['3'] ?? '',            
        'email' => $attributes['email'] ?? '',
        'mobile' => $attributes['mobile'] ?? '',
        ]);

        $bpga = BizPanGstApi::create([
                'file_name'=>'PAN to GST for biz_id '.$business->biz_id.' (no API hit)',
                'status'=>1,
                'created_at' => \Carbon\Carbon::now(),
                'created_by'=>Auth::user()->user_id
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
                'cin'=>(isset($attributes['biz_cin'])? $attributes['biz_cin']: ''),
                'created_at' => \Carbon\Carbon::now(),
                'created_by'=>Auth::user()->user_id
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
                'created_at' => \Carbon\Carbon::now(),
                'created_by'=>Auth::user()->user_id
            ]);

        //entry for all GST against the PAN
        if($attributes['pan_api_res'] ?? NULL) {
            $pan_api_res = explode(',', rtrim($attributes['pan_api_res'],','));
            $data = [];
            foreach ($pan_api_res as $key=>$value) {
                $data[$key]['user_id']=$userId;
                $data[$key]['biz_id']=$business->biz_id;
                $data[$key]['type']=2;
                $data[$key]['pan_gst_hash']=$value;
                $data[$key]['status']=1;
                $data[$key]['parent_pan_gst_id']=$bpg->biz_pan_gst_id;
                $data[$key]['created_at']=\Carbon\Carbon::now();
                $data[$key]['created_by']=Auth::user()->user_id;
                $data[$key]['biz_pan_gst_api_id']=0;
            }
            BizPanGst::insert($data);
        }

        if($attributes['cin_api_res'] ?? NULL) {
            $cin_api_res = explode(',', rtrim($attributes['cin_api_res'],','));
            $dataCin = [];
            foreach ($cin_api_res as $key=>$value) {
                $dataCin[$key]['biz_id'] = $business->biz_id;
                $dataCin[$key]['cin'] = $value;
                $dataCin[$key]['is_active'] = 1;
                $dataCin[$key]['created_by'] = Auth::user()->user_id;
                $dataCin[$key]['created_at'] = \Carbon\Carbon::now();
            }
            BizEntityCin::insert($dataCin);
        }
        // insert into rta_app table
        $app = Application::create([
            'user_id'=>$userId,
            'biz_id'=>$business->biz_id,
            // 'loan_amt'=>str_replace(',', '', $attributes['loan_amount']),
            'created_by'=>Auth::user()->user_id
        ]);

        if(isset($attributes['product_id'])){

            $product_ids = $attributes['product_id'];
          
            $product_ids = array_filter($product_ids, function($var){
               return (isset($var['checkbox']))?true:false; 
            });
            array_walk($product_ids, function (&$var , $key) {
                $var['loan_amount'] = str_replace(',', '', $var['loan_amount']);
                unset($var['checkbox']);
            });

            // insert in rta_app_product table
            $app->products()->sync($product_ids);

        }
    

        Business::where('biz_id', $business->biz_id)->update([
            'panno_pan_gst_id'=>$bpg->biz_pan_gst_id,
            'is_pan_verified'=>1,
            'gstno_pan_gst_id'=>0,
            'is_gst_verified'=>1,
            ]);

        //insert address into rta_biz_addr
        $address_data=[];
        array_push($address_data, array('biz_id'=>$business->biz_id, 'addr_1'=> $attributes['biz_address'],'city_name'=>$attributes['biz_city'],'state_id'=>$attributes['biz_state'],'pin_code'=>$attributes['biz_pin'],'address_type'=>0, 'location_id'=>$attributes['location_id'], 'created_at'=>\Carbon\Carbon::now(),'created_by'=>Auth::user()->user_id,'rcu_status'=>0));
        if(Auth::user()->anchor_id == config('common.LENEVO_ANCHOR_ID')) {
            for($i=0; $i <=3 ; $i++) { 
                $temp = array('biz_id'=>$business->biz_id, 'addr_1'=> null,'city_name'=> null,'state_id'=>null,'pin_code'=>null,'location_id'=>null,'address_type'=>($i+1),'created_at'=>\Carbon\Carbon::now(),'created_by'=>Auth::user()->user_id,'rcu_status'=>0);
                array_push($address_data, $temp);
            }
        } else {
            for($i=0; $i <=3 ; $i++) { 
                $temp = array('biz_id'=>$business->biz_id, 'addr_1'=> $attributes['biz_other_address'][$i],'city_name'=>$attributes['biz_other_city'][$i],'state_id'=>$attributes['biz_other_state'][$i],'pin_code'=>$attributes['biz_other_pin'][$i],'location_id'=>$attributes['location_other_id'][$i],'address_type'=>($i+1),'created_at'=>\Carbon\Carbon::now(),'created_by'=>Auth::user()->user_id,'rcu_status'=>0);
                array_push($address_data, $temp);
            }
        }
        BusinessAddress::insert($address_data);
        return ['biz_id'=>$business->biz_id,'app_id'=>$app->app_id];
    }        
    
    public static function getApplicationById($bizId){
        return Business::with('address.activeFiAddress')->where('biz.biz_id', $bizId)->first();
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
    
    public function segment(){
        return $this->hasOne('App\Inv\Repositories\Models\Master\Segment','id','biz_segment');
    }
    public function address(){
        return $this->hasMany('App\Inv\Repositories\Models\BusinessAddress','biz_id','biz_id')->where('biz_owner_id', null);
    }

    public function gsts(){
        return $this->hasMany('App\Inv\Repositories\Models\BizPanGst','biz_id','biz_id')->where(['type'=>2, 'biz_owner_id'=>null])->where('parent_pan_gst_id','<>',0);
    }
    public function cins(){
        return $this->hasMany('App\Inv\Repositories\Models\BizEntityCin','biz_id','biz_id')->where('is_active',1);
    }

    public function pan(){
        return $this->belongsTo('App\Inv\Repositories\Models\BizPanGst','biz_id','biz_id')->where(['type'=>1, 'biz_owner_id'=>null]);
    }

    public function gst(){
        return $this->belongsTo('App\Inv\Repositories\Models\BizPanGst','biz_id','biz_id')->where(['type'=>2, 'biz_owner_id'=>null, 'parent_pan_gst_id'=>0]);
    }
    public function cin(){
        return $this->belongsTo('App\Inv\Repositories\Models\BizPanGst','biz_id','biz_id')->where(['type'=>1, 'biz_owner_id'=>null, 'parent_pan_gst_id'=>0]);
    }

    public static function getCompanyDataByBizId($biz_id)
    {
        $arrData = self::select('biz.biz_id','biz.biz_entity_name','biz_pan_gst.pan_gst_hash','biz.cibil_score','biz_pan_gst.cin', 'biz.is_cibil_pulled','biz.date_of_in_corp')
        ->join('biz_pan_gst', 'biz_pan_gst.biz_pan_gst_id', '=', 'biz.panno_pan_gst_id')
        ->where('biz.biz_id', $biz_id)
        ->get();
        return $arrData;
    }

    public static function getAddressByUserId($user_id, array $biz_id_arr = [], array $where = []) {
        $arrData = self::select('biz.biz_id','biz.biz_entity_name', 'mst_state.name as state_name', 'biz_addr.*')
        ->join('biz_addr', 'biz_addr.biz_id', '=', 'biz.biz_id')
        ->join('mst_state', 'mst_state.id', '=', 'biz_addr.state_id')
        ->where(['biz.user_id' => $user_id, 'biz_addr.is_default' => 1, 'biz_addr.rcu_status' => 1, 'biz_addr.is_active' => 1])
        ->whereIn('biz_addr.biz_id', $biz_id_arr)
        ->where($where)
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
        'turnover_amt'=>($attributes['biz_turnover'])? str_replace(',', '', $attributes['biz_turnover']): 0,
        //'tenor_days'=>$attributes['tenor_days'],
        'biz_constitution'=>$attributes['biz_constitution'],
        'biz_segment'=>$attributes['segment'],
        'share_holding_date'=> isset($attributes['share_holding_date']) ? Carbon::createFromFormat('d/m/Y', $attributes['share_holding_date'])->format('Y-m-d') : null,
        'busi_pan_comm_date'=> isset($attributes['busi_pan_comm_date']) ? Carbon::createFromFormat('d/m/Y', $attributes['busi_pan_comm_date'])->format('Y-m-d') : null,
        'org_id'=>1,
        'msme_type' => $attributes['msme_type'],
        'msme_no' => $attributes['msme_no'],
        'updated_by'=>Auth::user()->user_id,
        'label_1' => $attributes['label']['1'] ?? '',
        'label_2' => $attributes['label']['2'] ?? '',
        'label_3' => $attributes['label']['3'] ?? '',
        'email' => $attributes['email'] ?? '',
        'mobile' => $attributes['mobile'] ?? '',
        ]);

        if(isset($attributes['is_gst_manual']) && $attributes['is_gst_manual']=='1'){
            if(isset($attributes['biz_gst_number']) && !empty($attributes['biz_gst_number'])){
                $bizpangst = BizPanGst::where(['biz_id'=>$bizId,'type'=>'2', 'parent_pan_gst_id'=>'0']);
                $bizpangst ->update(['pan_gst_hash'=>$attributes['biz_gst_number']]);                
            }            
        }

        if(!empty($attributes['pan_api_res'])){
            BizPanGst::where(['biz_id'=>$bizId,'biz_owner_id'=>null])->delete();
            $bpga = BizPanGstApi::create([
                    'file_name'=>'PAN to GST for biz_id '.$biz_id.' (no API hit)',
                    'status'=>1,
                    'created_by'=>Auth::user()->user_id
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
                    'cin'=>(isset($attributes['biz_cin'])? $attributes['biz_cin']: ''),
                    'created_at' => \Carbon\Carbon::now(),
                    'created_by'=>Auth::user()->user_id
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
                    'created_at' => \Carbon\Carbon::now(),
                    'created_by'=>Auth::user()->user_id
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
                $data[$key]['created_at']=\Carbon\Carbon::now();
                $data[$key]['created_by']=Auth::user()->user_id;
                $data[$key]['biz_pan_gst_api_id']=0;
            }
            BizPanGst::insert($data);

            $business->update([
                'panno_pan_gst_id'=>$bpg->biz_pan_gst_id,
                'is_pan_verified'=>1,
                'gstno_pan_gst_id'=>0,
                'is_gst_verified'=>1,
                ]);
        }else if(empty($attributes['pan_api_res'])){
            //update for parent GST
            BizPanGst::where(['type'=>2,'biz_id'=>$bizId, 'parent_pan_gst_id'=>0, 'biz_owner_id'=>null])->update([
                    'pan_gst_hash'=>$attributes['biz_gst_number'],
                    'updated_at' => \Carbon\Carbon::now(),
                    'updated_by'=>Auth::user()->user_id
                ]);

        }

        //update for CIN
        BizPanGst::where(['type'=>1,'biz_id'=>$bizId, 'parent_pan_gst_id'=>0, 'biz_owner_id'=>null])->update([
                'cin'=>(isset($attributes['biz_cin']))? $attributes['biz_cin']: NULL,
                'updated_by'=>Auth::user()->user_id
            ]);

        // update into rta_app table
        $app = Application::where('biz_id',$bizId)->first();
        $app->update([
                //'loan_amt'=>str_replace(',', '', $attributes['loan_amount']),
                'updated_by'=>Auth::user()->user_id
            ]);


        if(isset($attributes['product_id'])){

            $product_ids = $attributes['product_id'];
          
            $product_ids = array_filter($product_ids, function($var){
               return (isset($var['checkbox']))?true:false; 
            });
            array_walk($product_ids, function (&$var , $key) {
                $var['loan_amount'] = str_replace(',', '', $var['loan_amount']);
                unset($var['checkbox']);
            });

            // insert in rta_app_product table
            $app->products()->sync($product_ids);

        }


        //get id from address and then update address into rta_biz_addr
        $biz_addr_ids = BusinessAddress::where('biz_id',$bizId)->pluck('biz_addr_id');
        $address_data=[];
        BusinessAddress::where('biz_addr_id',$biz_addr_ids[0])->update(
            array('addr_1'=> $attributes['biz_address'],'city_name'=>$attributes['biz_city'],'state_id'=>$attributes['biz_state'],'pin_code'=>$attributes['biz_pin'], 'location_id'=>$attributes['location_id'], 'updated_at' => \Carbon\Carbon::now(),'updated_by'=>Auth::user()->user_id)
            );
        if(Auth::user()->anchor_id != config('common.LENEVO_ANCHOR_ID')) {
            for ($i=0; $i <=3 ; $i++) { 
                $temp = array('addr_1'=> $attributes['biz_other_address'][$i],'city_name'=>$attributes['biz_other_city'][$i],'state_id'=>$attributes['biz_other_state'][$i],'pin_code'=>$attributes['biz_other_pin'][$i],'location_id'=>$attributes['location_other_id'][$i],'updated_at' => \Carbon\Carbon::now(),'created_by'=>Auth::user()->user_id);
                BusinessAddress::where('biz_addr_id',$biz_addr_ids[$i+1])->update($temp);
            }
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
        $arrData = self::select('mst_industry.name as industryType','mst_biz_constitution.name', 'users.email', 'users.mobile_no')
        ->leftjoin('mst_industry', 'mst_industry.id', '=', 'biz.nature_of_biz')
        ->leftjoin('mst_biz_constitution', 'mst_biz_constitution.id', '=', 'biz.biz_constitution')
        ->leftjoin('users', 'users.user_id', '=', 'biz.user_id')
        ->where('biz.biz_id', $biz_id)
        ->first();
        return $arrData;
    }

    public function  LmsUser() {
         return $this->belongsTo('App\Inv\Repositories\Models\LmsUser','user_id','user_id');
    }

    public static function searchBusiness($search) {
      return  self::with('LmsUser')->where("biz_entity_name","LIKE","{$search}%")->groupBy('user_id')->get();
    }
    
    public static function getBizDataByPan($pan, $userId=null) {
        $query = self::select('users.user_id as user_id','users.mobile_no as mobile_no','users.email as email','biz.biz_id','biz.biz_entity_name','biz_pan_gst.pan_gst_hash','biz.cibil_score','biz_pan_gst.cin', 'biz.is_cibil_pulled','biz.msme_no')
        ->join('biz_pan_gst', 'biz_pan_gst.biz_pan_gst_id', '=', 'biz.panno_pan_gst_id')
        ->join('users', 'users.user_id','=','biz.user_id')                
        ->where('biz_pan_gst.pan_gst_hash', $pan);
        if (!is_null($userId)) {
            $query->where('biz.user_id', $userId);
        }         
        $arrData = $query->get();
        return $arrData;
    }

    public static function getlatestBizDataByPan($pan, $userId=null){

        $query = self::select('users.user_id as user_id','users.mobile_no as mobile_no','users.email as email','biz.biz_id','biz.biz_entity_name','biz_pan_gst.pan_gst_hash','biz.cibil_score','biz_pan_gst.cin', 'biz.is_cibil_pulled','biz.msme_no')
        ->join('biz_pan_gst', 'biz_pan_gst.biz_pan_gst_id', '=', 'biz.panno_pan_gst_id')
        ->join('users', 'users.user_id','=','biz.user_id')                
        ->where('biz_pan_gst.pan_gst_hash', $pan);
        if (!is_null($userId)) {
            $query->where('biz.user_id', $userId);
        }         
        $arrData = $query->orderBy('biz.biz_id','DESC')->first();
        return $arrData;
    }

    public function constitution() {
       return $this->belongsTo('App\Inv\Repositories\Models\Master\Constitution', 'biz_constitution', 'id');
    }

    public function users() {
       return $this->belongsTo(User::Class, 'user_id', 'user_id');
    }

    public function industryType() {
       return $this->belongsTo(Industry::Class, 'nature_of_biz', 'id');
    }


     public static function getBizDataByUserId($userId) {
        $query = self::select('biz.biz_id','biz.biz_entity_name')
        ->where('biz.user_id', $userId);
        $arrData = $query->get();
        return $arrData;
    }

    public function appWithUser(){
        return $this->belongsTo('App\Inv\Repositories\Models\Application','user_id','user_id');
    }
    
    function transactions()
    {
        return $this->hasMany('App\Inv\Repositories\Models\Lms\Transactions', 'user_id','user_id');
    } 
    
    public function sanctionDate() {
        return $this->belongsTo('App\Inv\Repositories\Models\AppStatusLog', 'user_id', 'user_id')->where('status_id', '50');
    } 
    
    public function prgmLimit()
    {
        return $this->hasOne('App\Inv\Repositories\Models\AppProgramLimit', 'biz_id')->where(['product_id' => 1]);
    }    

}
