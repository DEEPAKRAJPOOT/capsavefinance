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
        'rcu_status',
        'is_default',
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

    public static function getAddressforAgencyFI($biz_id){
        $address = BusinessAddress::whereHas('activeFiAddress')->where('biz_id', $biz_id)->where('addr_1', '<>', null)->get();
        return $address;
    }

    public function cmFiStatus(){
        return $this->hasOne('App\Inv\Repositories\Models\FiAddress','biz_addr_id','biz_addr_id')->where('is_active',1);
    }

    public function activeFiAddress(){
        return $this->hasOne('App\Inv\Repositories\Models\FiAddress','biz_addr_id','biz_addr_id')->where(['is_active'=>1, 'agency_id'=>\Auth::user()->agency_id]);
    }

    /**
     * Business address
     * 
     * @param integer $user_id
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public static function addressGetCustomer($user_id, $biz_id)
    {


        $result =  self::select(
            'biz_addr.addr_1 as Address',
            'c.user_id as Customer_id',
            'biz_addr.city_name as City',
            'biz_addr.rcu_status',
            'biz_addr.biz_addr_id',
            'b.name as State',
            'biz_addr.pin_code as Pincode',
            'biz_addr.is_default'
        )
            ->leftjoin('mst_state as b', 'biz_addr.state_id', '=', 'b.id')
            ->leftjoin('biz as c', 'biz_addr.biz_id', '=', 'c.biz_id')
            //->where('c.user_id', '=', $user_id)
            ->where('biz_addr.biz_id', $biz_id)
            ->whereNotNull('addr_1')
            ->whereNotNull('city_name')
            ->whereNotNull('pin_code')
            ->groupBy('user_id', 'Address', 'City', 'State', 'Pincode')
            ->orderBy('biz_addr_id', 'DESC');
        return $result;
    }

    public static function saveBusinessAddress($data, $limit_id)
    {
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        return self::updateOrCreate(['biz_addr_id' => $limit_id], $data);
    }

    public static function setDefaultAddress($attributes, $where = [])
    {
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        if (empty($attributes)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }


        $result = \DB::table('biz_addr');
        if (!empty($where)) {
            $result = $result->where($where);
        }
        $result = $result->update($attributes);
        return $result ?: false;
    }
}