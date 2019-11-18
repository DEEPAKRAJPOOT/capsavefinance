<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Notifications\Notifiable;
use App\Inv\Repositories\Models\BusinessAddress;

class Business extends Model
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
        'date_of_in_corp'=>$attributes['incorporation_date'],
        'entity_type_id'=>$attributes['entity_type_id'],
        'nature_of_biz'=>$attributes['biz_type_id'],
        'turnover_amt'=>$attributes['biz_turnover'],
        'segment_id'=>$attributes['segment'],
        'org_id'=>1,
        'created_by'=>$userId,
        //'biz_pan_id'=>$attributes['zzz'],
        //'is_pan_verified'=>$attributes['zzz'],
        //'biz_gst_id'=>$attributes['zzz'],
        //'is_gst_verified'=>$attributes['zzz'],
        ]);

        $bpga_id = DB::table('biz_pan_gst_api')->insertGetId([
                'file_name'=>'file name goes here',
                'status'=>1,
                'created_by'=>$userId
            ]);
        //entry for parent
        $pan_id = DB::table('biz_pan_gst')->insertGetId([
                'user_id'=>$userId,
                'biz_id'=>$business->biz_id,
                'type'=>1,
                'pan_gst_hash'=>$attributes['biz_pan_number'],
                'status'=>1,
                'parent_pan_gst_id'=>0,
                'biz_pan_gst_api_id'=>$bpga_id,
                'cin'=>$attributes['biz_cin'],
                'created_by'=>$userId
            ]);

        //entry for child
        $pan_api_res = explode(',', rtrim($attributes['pan_api_res'],','));
        $data = [];
        foreach ($pan_api_res as $key=>$value) {
            $data[$key]['user_id']=$userId;
            $data[$key]['biz_id']=$business->biz_id;
            $data[$key]['type']=2;
            $data[$key]['pan_gst_hash']=$value;
            $data[$key]['status']=1;
            $data[$key]['parent_pan_gst_id']=$pan_id;
            $data[$key]['created_by']=$userId;
            $data[$key]['biz_pan_gst_api_id']=0;
            $data[$key]['created_by']=$userId;
        }
        DB::table('biz_pan_gst')->insert($data);


        $app_id = DB::table('app')->insertGetId([
                'user_id'=>$userId,
                'biz_id'=>$business->biz_id,
                'loan_amt'=>$attributes['loan_amount'],
                'created_by'=>$userId
            ]);

        Business::where('biz_id', $business->biz_id)->update([
            'panno_pan_gst_id'=>$pan_id,
            'is_pan_verified'=>1,
            'gstno_pan_gst_id'=>0,
            'is_gst_verified'=>1,
            ]);

        //insert address
        $address_data = array(
            array('biz_id'=>$business->biz_id, 'addr_1'=> $attributes['biz_address'],'city_name'=>$attributes['biz_city'],'state_name'=>$attributes['biz_state'],'pin_code'=>$attributes['biz_pin'],'address_type'=>0,'created_by'=>$userId,'rcu_status'=>0),
            array('biz_id'=>$business->biz_id, 'addr_1'=> $attributes['biz_corres_address'],'city_name'=>$attributes['biz_corres_city'],'state_name'=>$attributes['biz_corres_state'],'pin_code'=>$attributes['biz_corres_pin'],'address_type'=>1,'created_by'=>$userId,'rcu_status'=>0),
        );

        BusinessAddress::insert($address_data);

        return ['biz_id'=>$business->biz_id,'app_id'=>$app_id];
    }

    public static function getApplicationById($bizId){
        //dd($bizId);
        $bizData = self::select('app.app_id', 'app.loan_amt','biz.biz_entity_name','biz.date_of_in_corp','biz.entity_type_id','biz.turnover_amt','biz.nature_of_biz','biz.org_id','bpg1.cin','bpg1.pan_gst_hash')
                ->join('app', 'biz.biz_id', '=', 'app.biz_id')
                ->join('biz_pan_gst as bpg1', 'bpg1.biz_pan_gst_id', '=', 'biz.panno_pan_gst_id')
                //->join('biz_pan_gst as bpg2', 'bpg2.biz_pan_gst_id', '=', 'biz.gstno_pan_gst_id')
                ->where('biz.biz_id', $bizId)
                ->get();
        return $bizData;
    }
}