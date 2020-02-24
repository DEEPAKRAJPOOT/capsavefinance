<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Notifications\Notifiable;
//use App\Inv\Repositories\Models\BusinessAddress;
use App\Inv\Repositories\Factory\Models\BaseModel;
use Helpers;
use Auth;

class GroupCompanyExposure extends BaseModel
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'group_company_exposure';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'group_company_expo_id';

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
        'biz_id',
        'app_id',
        'group_Id',
        'group_company_name',
        'sanction_limit',
        'outstanding_exposure',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

 

    public static function saveGroupCompany($attributes){
        $response = self::create($attributes);
        return  $response ? true : false;

    }

}



