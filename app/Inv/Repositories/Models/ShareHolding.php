<?php

namespace App\Inv\Repositories\Models;

use DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class ShareHolding extends Authenticatable
{

    use Notifiable;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'corp_shareholding';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'corp_shareholding_id';

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
        'user_id',
        'company_name',
        'passport_no',
        'share_percentage',
        'actual_share_percent',
        'share_value',
        'share_type',
        'share_parent_id',
        'share_level',
        'owner_kyc_id',
        'created_at',
        'updated_at',    
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

    public static function createShareHolding($attributes,$id)
    {
        $res=ShareHolding::where('user_id', $id)->first();
        //dd($attributes);
        if($res){
           
            $result = ShareHolding::where('user_id', $id)->update($attributes);

            return $result ?: false;
        } else {
           // dd($attributes);
            return ShareHolding::create($attributes);
        }
    }
    
    
    /**
     * storeData
     * @param
     * @return array
     * @since 0.1
     * @author Arvind soni
     */
    public static function storeData($inputData,$id='') {
        try {
            $data = self::updateOrCreate(['corp_shareholding_id' => (int) $id],$inputData);
            return ($data ?: false);
        } catch (\Exception $e) {
            $data['errors'] = $e->getMessage();
        }
    }
    
    /**
     * storeData
     * @param
     * @return array
     * @since 0.1
     * @author Arvind soni
     */
    public static function getData($id='') {
        try {
            $result=self::where('corp_shareholding_id','=',$id)->first();
            
            return ($result?:false); 
        } catch (\Exception $e) {
            $data['errors'] = $e->getMessage();
        }
    }
    public static function getHigestLevelShareData($user_id,$share_type){
        try{

            $obmax=self::select('share_level')->where('user_id',$user_id)->skip(0)->take(1)->orderBy('share_level','DESC')->first();

            $result=self::select('share_level','share_parent_id','corp_shareholding_id','company_name','share_type')
                    ->where('share_type','=',$share_type)
                    ->where('user_id','=',$user_id)
                    ->where('share_level','=',$obmax->share_level)->get();

            return ($result?:false);
        }
        catch(\Exception $e){
            $data['errors'] = $e->getMessage();
        }

    }

    
     public static function getBeneficiaryOwnersData($user_id){
        try{
            $result=self::select('share_level','share_parent_id','corp_shareholding_id','company_name','share_type','owner_kyc_id','user_id','share_percentage','actual_share_percent')
                    ->where('share_type','=','1')
                    ->where('user_id','=',$user_id)
                    ->orderBy('share_level','asc')
                    ->get();

            return ($result?:false);
        }
        catch(\Exception $e){
            $data['errors'] = $e->getMessage();
        }

    }
    
    public static function getShareHolderData($user_id){
        try{
            $result=self::select('share_level','share_parent_id','corp_shareholding_id','company_name','share_type','owner_kyc_id','user_id','share_percentage','actual_share_percent')
                    ->where('user_id','=',$user_id)
                    ->orderBy('share_level','asc')
                    ->get();
            
            return ($result?:false); 
        }
        catch(\Exception $e){
            $data['errors'] = $e->getMessage();
        }
    }
    
    
    
    
    
  

   
   

}