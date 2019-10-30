<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Notifications\Notifiable;

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
        'org_id',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    public static function creates(){
        DB::table('rta_biz_gst')->insertGetId([]);
        DB::table('rta_biz_pan')->insertGetId([]);
        //add add both

        return Business::create([
        'user_id'=>$userId,
        'biz_entity_name'=>$attributes->biz_entity_name,
        'date_of_in_corp'=>$attributes->date_of_in_corp,
        'entity_type_id'=>$attributes->entity_type_id,
        'turnover_amt'=>$attributes->turnover_amt,
        'nature_of_biz'=>$attributes->zzz,
        'org_id'=>$attributes->zzz,
        'biz_pan_id'=>$attributes->zzz,
        'is_pan_verified'=>$attributes->zzz,
        'biz_gst_id'=>$attributes->zzz,
        'is_gst_verified'=>$attributes->zzz,
        'created_by'=>$$userId,
        ])
    }



}
