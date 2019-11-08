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
        'nature_of_biz_id',
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
        'nature_of_biz_id'=>$attributes['biz_type_id'],
        'turnover_amt'=>$attributes['biz_turnover'],
        'segment_id'=>$attributes['segment'],
        'org_id'=>1,
        'created_by'=>$userId,
        //'biz_pan_id'=>$attributes['zzz'],
        //'is_pan_verified'=>$attributes['zzz'],
        //'biz_gst_id'=>$attributes['zzz'],
        //'is_gst_verified'=>$attributes['zzz'],
        ]);

        $gst_id = DB::table('biz_gst')->insertGetId([
                'user_id'=>$userId,
                'biz_id'=>$business->biz_id,
                'gst_hash'=>$attributes['biz_gst_number'],
                'cin'=>$attributes['biz_cin'],
                'status'=>0,
                'created_by'=>$userId
            ]);
        $pan_id = DB::table('biz_pan')->insertGetId([
                'user_id'=>$userId,
                'biz_id'=>$business->biz_id,
                'pan_hash'=>$attributes['biz_pan_number'],
                'status'=>0,
                'created_by'=>$userId
            ]);
        $app_id = DB::table('biz_app')->insertGetId([
                'user_id'=>$userId,
                'biz_id'=>$business->biz_id,
                'loan_amt'=>$attributes['loan_amount'],
                'created_by'=>$userId
            ]);

        Business::where('biz_id', $business->biz_id)->update([
            'biz_pan_id'=>$pan_id,
            'is_pan_verified'=>0,
            'biz_gst_id'=>$gst_id,
            'is_gst_verified'=>0,
            ]);

        //insert address
        $address_data = array(
            array('biz_id'=>$business->biz_id, 'addr_1'=> $attributes['biz_address'],'city_name'=>$attributes['biz_city'],'state_name'=>$attributes['biz_state'],'pin_code'=>$attributes['biz_pin'],'address_type'=>0,'created_by'=>$userId),
            array('biz_id'=>$business->biz_id, 'addr_1'=> $attributes['biz_corres_address'],'city_name'=>$attributes['biz_corres_city'],'state_name'=>$attributes['biz_corres_state'],'pin_code'=>$attributes['biz_corres_pin'],'address_type'=>1,'created_by'=>$userId),
        );

        BusinessAddress::insert($address_data);

        return $business;
    }
}