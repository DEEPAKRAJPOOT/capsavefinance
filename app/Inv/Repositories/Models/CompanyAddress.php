<?php

namespace App\Inv\Repositories\Models;

use DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\CorrespondCompanyAddress;
class CompanyAddress extends Authenticatable
{

    use Notifiable;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'corp_addresses';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'corp_addr_id';

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
    protected $fillable = [
        'user_kyc_id',
        'user_id',
        'country_id',
        'region',
        'city_id',
        'building',
        'street',
        'postal_code',
        'po_box',
        'email',
        'telephone',
        'mobile',
        'fax',
        
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




    public function createCompanyAddress($request,$userKycid)
    {
        //dd($request->all());
        $res=CompanyAddress::where('user_kyc_id', $userKycid)->first();
        $id=Auth()->user()->user_id;
        $attributes=[
                   'user_kyc_id'=>$userKycid,
                   'user_id'=>$id,
                    'country_id'=>$request->country,
                    'city_id'=>$request->city,
                    'region'=>$request->region,
                    'building'=>$request->building,
                    'street'=>$request->street,
                    'postal_code'=>$request->postalcode,
                    'po_box'=>$request->pobox,
                    'email'=>$request->email,
                    'telephone'=>$request->telephone,
                    'mobile'=>$request->mobile,
                    'fax'=>$request->faxno,
                ];
        $correadd=[
                    'user_id'=>$id,
                    'corre_country'=>$request->corr_country,
                    'corre_city'=>$request->corr_city,
                    'corre_region'=>$request->corr_region,
                    'corre_building'=>$request->corr_building,
                    'corre_floor'=>$request->corr_floor,
                    'corre_street'=>$request->corr_street,
                    'corre_postal_code'=>$request->corr_postal,
                    'corre_po_box'=>$request->corr_pobox,
                    'corre_email'=>$request->corr_email,
                    'corre_telephone'=>$request->corr_tele,
                    'corre_mobile'=>$request->corr_mobile,
                    'corre_fax'=>$request->corr_fax,
                ];        

        if($res){
           
                CompanyAddress::where('user_kyc_id', $userKycid)->update($attributes);

                $result =CorrespondCompanyAddress::updates($correadd,$id);

                return $result ?: false;
        } else {

                $address=CompanyAddress::create($attributes);
               

                
                $correadd['corp_addr_id']=$address->corp_addr_id;
                $result=CorrespondCompanyAddress::creates($correadd);
                
                return $result ?: false;
          }
    }
  

   public function getCorpAddress($userKycId)
   {

        $data=DB::table('corp_addresses')
            ->join('corp_corres_address','corp_corres_address.corp_addr_id', '=', 'corp_addresses.corp_addr_id')
               ->where('corp_addresses.user_kyc_id',$userKycId)
               ->first();
        return $data ?: false;
   }
   

}