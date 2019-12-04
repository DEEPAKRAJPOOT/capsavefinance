<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Inv\Repositories\Factory\Models\BaseModel;
use DB;

class BusinessAddress extends BaseModel
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'biz_addr';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'biz_addr_id';

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
        'biz_id',
        'biz_owner_id',
        'addr_1',
        'addr_2',
        'city_name',
        'state_id',
        'pin_code',
        'address_type',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    public static function getFiLists($dataArr){
        //$address = BusinessAddress::where(['biz_id'=>$dataArr->biz_id])->get();
        //return $address;

        $bizAddr = BusinessAddress::select('biz_addr.biz_addr_id as id', 'biz.biz_entity_name as name', 'biz_addr.address_type as address_type', DB::raw("'business' as mode"), DB::raw("CONCAT(rta_biz_addr.addr_1,rta_biz_addr.city_name,rta_mst_state.name,rta_biz_addr.pin_code) AS address"))
                ->leftJoin('mst_state', 'mst_state.id', '=', 'biz_addr.state_id')
                ->leftJoin('biz', 'biz.biz_id', '=', 'biz_addr.biz_id')
                ->where('biz_addr.biz_id' ,$dataArr->biz_id)->where('biz_addr.addr_1', '<>', null);
        $address = DB::table('biz_owner')
            ->select('biz_owner.biz_owner_id as id', 'biz_owner.first_name as name', DB::raw("5 as address_type"), DB::raw("'promoter' as mode"), 'biz_owner.owner_addr as address')
            ->where('biz_id' ,$dataArr->biz_id)
            ->union($bizAddr)
            ->get();
        return ($address);


    }

    public function state(){
        return $this->belongsTo('App\Inv\Repositories\Models\Master\State','state_id','id');
    }

    public function business(){
        return $this->belongsTo('App\Inv\Repositories\Models\Business','biz_id','biz_id');
    }

    public function owner(){
        return $this->belongsTo('App\Inv\Repositories\Models\BizOwner','biz_owner_id','biz_owner_id');
    }

    public function fiAddress(){
        return $this->hasMany('App\Inv\Repositories\Models\FiAddress','biz_addr_id','biz_addr_id');
    }

    public static function getAddressforFI($biz_id){
        $address = BusinessAddress::where('biz_id', $biz_id)->where('addr_1', '<>', null)->get();
        return $address;
    }
}