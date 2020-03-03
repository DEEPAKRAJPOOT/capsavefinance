<?php
namespace App\Inv\Repositories\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Auth;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use App\Inv\Repositories\Models\AppDocument;
use App\Inv\Repositories\Models\AppDocumentFile;
use App\Inv\Repositories\Models\BizPanGstApi;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\BizPanGst;
use App\Inv\Repositories\Models\BusinessAddress;
use App\Inv\Repositories\Factory\Models\BaseModel;

class BizOwner extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'biz_owner';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'biz_owner_id';

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
        'user_id',
        'is_pan_verified',
        'first_name',
        'is_promoter',
        'mobile_no',
        'date_of_birth',
        'gender',
        'share_per',
        'edu_qualification',
        'designation',
        'comment',
        'other_ownership',
        'networth',
        'pan_number',
        'mobile',
        'pan_card',
        'driving_license',
        'voter_id',
        'passport',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    
    ];
    /* get owner api details */
    /* created by gajendra chauhan   */
   public static function getOwnerApiDetails($bizId)
   {
      $biz_id = $bizId['biz_id'];
      return BizOwner::with('pan')->with('address')->with('businessApi.karza')->with('document.userFile')->where('biz_id', $biz_id)->get();
   }
    /* Relation of Owner and Gst Api relation*/
    /* created by gajendra chauhan   */
   public function pan()
   {
      return $this->belongsTo('App\Inv\Repositories\Models\BizPanGst', 'biz_pan_gst_id','biz_pan_gst_id')->where(['type' => 1]);  
       
   }
   
    /* Relation of Owner and Gst Api relation*/
    /* created by gajendra chauhan   */
   public function businessApi()
   {
      return $this->hasMany('App\Inv\Repositories\Models\BizApi', 'biz_owner_id','biz_owner_id');  
       
   }
   
      /* Relation of Owner and Gst Api relation*/
    /* created by gajendra chauhan   */
   public function address()
   {
      return $this->belongsTo('App\Inv\Repositories\Models\BusinessAddress', 'biz_owner_id','biz_owner_id');  
       
   }   
    /* Relation of Owner and Gst Api relation*/
    /* created by gajendra chauhan   */
   public function document()
   {
      return $this->hasMany('App\Inv\Repositories\Models\AppDocumentFile', 'biz_owner_id','biz_owner_id');  
       
   }
   /* Relation of Owner and  Aplication relation*/
    /* created by gajendra chauhan   */
    public static  function getOwnerByBizId($bizId){
        
        return BizOwner::where('biz_id', $bizId)->get();
      
    }

    public function app(){
        return $this->belongsTo('App\Inv\Repositories\Models\Application','biz_id','biz_id');
    }
   
/* save biz owner data*/
    /* By gajendra chauhan  */  
   public static function createsOwner($attributes)
    { 
        $userData  =  User::getUserByAppId($attributes['app_id']);
        $uid =  $userData->user_id;
        $i =0;
       $getOwnerId = []; 
       foreach($attributes['data'] as $key=>$val)
       {    ///// insert data onload promoter page/////////////////////
                $ownerInputArr =  BizOwner::create( ['biz_id' => $attributes['biz_id'],   
                'user_id' => $uid, 
                'first_name' =>  $val['first_name'],
                'date_of_birth' => ($val['dob'])? Carbon::createFromFormat('d/m/Y', $val['dob'])->format('Y-m-d'): NULL,
                'created_by' => Auth::user()->user_id]);
              /////get details for insert address for promoter///////////
              if($ownerInputArr)
              {    
                BusinessAddress::create(['addr_1'  =>  $val['address'],
                 'biz_id'  =>  $attributes['biz_id'],
                 'address_type'  =>  5,
                 'rcu_status'  =>   0,
                 'created_by'  => Auth::user()->user_id,
                 'biz_owner_id'  =>  $ownerInputArr->biz_owner_id]);
              }
            $getOwnerId[] = $ownerInputArr->biz_owner_id;  
            $i++;      
       } 
       
       return  $getOwnerId;
   }
   
    public static function creates($attributes)
    {
          //insert into rta_app_doc
          $userData  =  User::getUserByAppId($attributes['app_id']);
          $uid =  $userData->user_id;
          if($attributes['ownerid'][0]==null)
            {
                 $getRes =  self::saveOwnerPanApiRes($attributes, $attributes['biz_id']); 
            }
            else
            {
                $getRes =  self::savePanApiRes($attributes, $attributes['biz_id']); 
            }
         
          return true;

    }
  
    /*  Save Pan Api Response data //////////////////  */
  public static function savePanApiRes($attributes,$biz_id)
  {
      
    $count = count($attributes['first_name']);
    $userData  =  User::getUserByAppId($attributes['app_id']);
    $userId =  $userData->user_id;
    $updateCount =  count($attributes['ownerid']);
    $mytime = Carbon::now();
    $dateTime = $mytime->toDateTimeString();
     for ($i=0;$i<$count;$i++) 
     {  
         
           if($i < $updateCount)
           {
             $biz_owner_id =   $attributes['ownerid'][$i];
             $ownerInputArr =  BizOwner::where('biz_owner_id',$attributes['ownerid'][$i])->update( ['biz_id' => $biz_id,   
            'user_id' => $userId, 
            'first_name' => $attributes['first_name'][$i],
            'is_promoter' => isset($attributes['isShareCheck'][$i]) ? $attributes['isShareCheck'][$i] : 0,
            'mobile_no' => $attributes['mobile_no'][$i],     
            'date_of_birth' => ($attributes['date_of_birth'])? Carbon::createFromFormat('d/m/Y', $attributes['date_of_birth'][$i])->format('Y-m-d'): NULL,
            'gender' => $attributes['gender'][$i],
            'comment' => $attributes['comment'][$i],
            'is_pan_verified' => 1, 
            'share_per' => $attributes['share_per'][$i],
               'designation' => $attributes['designation'][$i],    
           /// 'edu_qualification' => $attributes['edu_qualification'][$i],
            'other_ownership' => $attributes['other_ownership'][$i],
            'networth' => str_replace(',','',$attributes['networth'][$i]),
            'pan_number' => isset($attributes['pan_no'][$i]) ? $attributes['pan_no'][$i] : null,
            'mobile'  =>  isset($attributes['mobile_no'][$i]) ? $attributes['mobile_no'][$i] : null,
            'pan_card'  =>isset($attributes['veripan'][$i]) ? $attributes['veripan'][$i] : null,
            'driving_license'  => isset($attributes['verifydl'][$i]) ? $attributes['verifydl'][$i] : null,
            'voter_id'  => isset($attributes['verifyvoter'][$i]) ? $attributes['verifyvoter'][$i] : null,
            'passport'  => isset($attributes['verifypassport'][$i]) ? $attributes['verifypassport'][$i] : null,
            'created_by' =>  Auth::user()->user_id]);
             ////////////////adress update
             
             BusinessAddress::where('biz_owner_id',$attributes['ownerid'][$i])->update(['addr_1'  =>  $attributes['owner_addr'][$i],
                 'updated_by'  => Auth::user()->user_id]);
              
           }
      else {
            $ownerInputArr =  BizOwner::create( ['biz_id' => $biz_id,   
            'user_id' => $userId, 
            'first_name' => $attributes['first_name'][$i],
            'is_promoter' => isset($attributes['isShareCheck'][$i]) ? $attributes['isShareCheck'][$i] : 0,
            'mobile_no' => isset($attributes['mobile_no'][$i]) ? $attributes['mobile_no'][$i] : null, 
            'date_of_birth' => ($attributes['date_of_birth'])? Carbon::createFromFormat('d/m/Y', $attributes['date_of_birth'][$i])->format('Y-m-d'): NULL,
            'gender' => $attributes['gender'][$i],
            'comment' => $attributes['comment'][$i],
            'is_pan_verified' => 1, 
            'share_per' => $attributes['share_per'][$i],
               'designation' => $attributes['designation'][$i],    
           /// 'edu_qualification' => $attributes['edu_qualification'][$i],
            'other_ownership' => $attributes['other_ownership'][$i],
            'networth' => str_replace(',','',$attributes['networth'][$i]),
            'pan_number' => isset($attributes['pan_no'][$i]) ? $attributes['pan_no'][$i] : null,
            'mobile'  =>  isset($attributes['mobile_no'][$i]) ? $attributes['mobile_no'][$i] : null,
            'pan_card'  =>isset($attributes['veripan'][$i]) ? $attributes['veripan'][$i] : null,
            'driving_license'  => isset($attributes['verifydl'][$i]) ? $attributes['verifydl'][$i] : null,
            'voter_id'  => isset($attributes['verifyvoter'][$i]) ? $attributes['verifyvoter'][$i] : null,
            'passport'  => isset($attributes['verifypassport'][$i]) ? $attributes['verifypassport'][$i] : null,
            'created_by' =>  Auth::user()->user_id]);
            $biz_owner_id  = $ownerInputArr->biz_owner_id;
            //////////////////////////////save address //////////////////
                if($biz_owner_id)
                {
                    BusinessAddress::create(['addr_1'  =>  $attributes['owner_addr'][$i],
                        'biz_id'  =>  $biz_id,
                        'address_type'  =>  5,
                        'rcu_status'  =>   0,
                        'created_by'  => Auth::user()->user_id,
                        'biz_owner_id'  =>  $biz_owner_id]);
                }
             
            }
        
      
     }

     return $ownerInputArr;
  }
  
  
      /*  Save Pan Api Response data //////////////////  */
  public static function saveOwnerPanApiRes($attributes,$biz_id)
  {
      
    $count = count($attributes['first_name']);
    $userData  =  User::getUserByAppId($attributes['app_id']);
    $userId =  $userData->user_id;
    $mytime = Carbon::now();
    $dateTime = $mytime->toDateTimeString();
     for ($i=0;$i<$count;$i++) 
     {  
            $ownerInputArr =  BizOwner::create( ['biz_id' => $biz_id,   
            'user_id' => $userId, 
            'first_name' => $attributes['first_name'][$i],
            'is_promoter' => ($attributes['isShareCheck'][$i]) ? $attributes['isShareCheck'][$i] : 0,
            'mobile_no' => $attributes['mobile_no'][$i], 
            'date_of_birth' =>($attributes['date_of_birth'])? Carbon::createFromFormat('d/m/Y', $attributes['date_of_birth'][$i])->format('Y-m-d'): NULL,
            'gender' => $attributes['gender'][$i],
            'comment' => $attributes['comment'][$i],
            'is_pan_verified' => 1, 
            'share_per' => $attributes['share_per'][$i],
            'designation' => $attributes['designation'][$i],    
           /// 'edu_qualification' => $attributes['edu_qualification'][$i],
            'other_ownership' => $attributes['other_ownership'][$i],
            'networth' =>  str_replace(',','',$attributes['networth'][$i]),
            'created_by' =>  Auth::user()->user_id]);
            $biz_owner_id  = $ownerInputArr->biz_owner_id;
            ///////////////// save promoter address/////////////////////
              if($biz_owner_id)
                {
                    BusinessAddress::create(['addr_1'  =>  $attributes['owner_addr'][$i],
                        'biz_id'  =>  $biz_id,
                        'address_type'  =>  5,
                        'rcu_status'  =>   0,
                        'created_by'  => Auth::user()->user_id,
                        'biz_owner_id'  =>  $biz_owner_id]);
                }
       
     }

     return $biz_owner_id;
  }
  
  public static function getAppId($uid)
  {
      $userId = Auth::user()->user_id;
      $res =  Application::where(['status' => 0,'user_id' => $uid])->first();
      return $res;
  }

  public static function getCompanyOwnerByBizId($biz_id)
    {
        $arrData = self::select('biz_owner.first_name','biz_owner.biz_owner_id','biz_owner.last_name','biz_owner.pan_number', 'biz_owner.email','biz_owner.mobile_no','biz_owner.cibil_score', 'biz_owner.is_cibil_pulled','biz_owner.is_promoter', 'biz_owner.gender', 'biz_owner.designation', 'biz_owner.share_per')
        ->where('biz_owner.biz_id', $biz_id)
        ->get();
        return $arrData;
    }

 
  public static function getBizOwnerDataByOwnerId($biz_owner_id)
  {
     $arrData = self::where('biz_owner.biz_owner_id', $biz_owner_id)->first();
        return $arrData;
  }



   
}