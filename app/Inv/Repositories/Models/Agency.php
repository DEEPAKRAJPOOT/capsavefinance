<?php

namespace App\Inv\Repositories\Models;

use Carbon\Carbon;
use DateTime;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use Illuminate\Database\Eloquent\Builder;

class Agency extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'agency';
    public $timestamps = false;
    public $userstamps = false;

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'agency_id';

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
        'comp_name',
        'comp_email',
        'comp_addr',
        'comp_state',
        'comp_city',
        'comp_zip',
        'comp_phone',
        'is_active',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    public function getFullnameAttribute(){
        return ucwords($this->f_name.' '.$this->m_name.' '.$this->l_name);
    }

    public function agencyType(){
        return $this->belongsToMany('App\Inv\Repositories\Models\Master\Status', 'agency_type', 'agency_id', 'type_id');
    }

    public static function creates($attributes){
        $agency = Agency::create($attributes);

        // insert in rta_agency_type table
        $agency->agencyType()->sync($attributes['type_id']);
        return $agency;
    }

    public static function updateAgency($attributes, $agency_id){
        $query = Agency::whereAgencyId($agency_id)->first();
        $agency = $query->update($attributes);

        // insert in rta_agency_type table
        $query->agencyType()->sync($attributes['type_id']);
        return $agency;
    }

    public static function getAllAgency($type=null){
        if(is_null($type) || $type == ''){
            return Agency::get();
        }else{
            return Agency::where('is_active',1)->whereHas('agencyType', function(Builder $query) use($type){$query->where('status_name', $type);})->get();
        }
    }

    // get all agency
    public static function getAgenciByAgenciId(int $id)
    {
       
         if (empty($id)) {
            throw new BlankDataExceptions(trans('error_messages.no_data_found'));
        }

        if (!is_int($id)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }
        $result = self::where('agency_id', $id)->first();
               
        return ($result ? : false);
    }

    public static function updateAgencyStatus($attributes = [], $conditions = [])
    {
        /**
         * Check Data is Array
         */
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }


        /**
         * Check Data is Array
         */
        if (!is_array($conditions)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        /**
         * Check Data is not blank
         */
        if (empty($conditions)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        $res = self::where($conditions)->update($attributes);

        return ($res ?: false);
    }



}