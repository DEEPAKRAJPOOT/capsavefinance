<?php

namespace App\Inv\Repositories\Models;

use DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\CompanyAddress;

class CorrespondCompanyAddress extends Authenticatable
{

    use Notifiable;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'corp_corres_address';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'corp_corres_addr_id';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $fillable = [
        'corp_addr_id',
        'user_id',
        'corre_country',
        'corre_city',
        'corre_region',
        'corre_building',
        'corre_floor',
        'corre_street',
        'corre_postal_code',
        'corre_po_box',
        'corre_email',
        'corre_telephone',
        'corre_mobile',
        'corre_fax',

        
        
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    

    /**
     * update user details
     *
     * @param integer $user_id     user id
     * @param array   $arrUserData user data
     *
     * @return boolean
     */


   

    public static function creates($correadd)
    {
       
      return CorrespondCompanyAddress::create($correadd);
        
    }
  
    public static function updates($correadd,$id){
    
       return  CorrespondCompanyAddress::where('user_id', $id)->update($correadd);
    }
   
   

}