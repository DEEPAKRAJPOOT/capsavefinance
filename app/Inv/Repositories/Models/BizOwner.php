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
        'biz_pan_gst_id',
        'is_pan_verified',
        'first_name',
        'is_promoter',
        'mobile_no',
        'date_of_birth',
        'gender',
        'share_per',
        'edu_qualification',
        'other_ownership',
        'networth',
        'owner_addr',
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
      return BizOwner::with('pan')->with('businessApi.karza')->with('document.userFile')->where('biz_id', $biz_id)->get();
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
       foreach($attributes['data'] as $key=>$val)
       {
              
                $ownerInputArr =  BizOwner::create( ['biz_id' => $attributes['biz_id'],   
                'user_id' => $uid, 
                'first_name' =>  $val['first_name'],
                'date_of_birth' => ($val['dob'])? Carbon::createFromFormat('d/m/Y', $val['dob'])->format('Y-m-d'): NULL,
                'owner_addr' => $val['address'],
              'created_by' => Auth::user()->user_id]);
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
         
          /*  
          $appDocCheck = AppDocument::where('user_id', $uid)
                    ->where('app_id', $attributes['app_id'])
                    ->count();
          if($appDocCheck == 0){
            $owner = AppDocument::insert([
              [
              'rcu_status' => 0,
              'user_id' => $uid,
              'app_id' => (int) $attributes['app_id'],
              'doc_id' => 4,
              'is_upload' => 0,
              'created_by' => Auth::user()->user_id,
              'updated_by' => Auth::user()->user_id
              ],
              [
              'rcu_status' => 0,
              'user_id' => $uid,
              'app_id' => (int) $attributes['app_id'],
              'doc_id' => 5,
              'is_upload' => 0,
              'created_by' => Auth::user()->user_id,
              'updated_by' => Auth::user()->user_id
              ],
              [
              'rcu_status' => 0,
              'user_id' => $uid,
              'app_id' => (int) $attributes['app_id'],
              'doc_id' => 6,
              'is_upload' => 0,
              'created_by' => Auth::user()->user_id,
              'updated_by' => Auth::user()->user_id
              ]
              ]);
          }
          */
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
     {  /* save response api data */
         
         $res = BizPanGstApi::create(['file_name' => $attributes['response'][$i],
         'created_at' => $dateTime,
         'created_by' => Auth::user()->user_id]); 
//         dd($attributes);
         /* save Owner api data */
        if($res->biz_pan_gst_api_id > 0){
            $bizPanRes =  BizPanGst::create( [   
           'user_id' => $userId, 
           'biz_id' => $biz_id,    
           'type' => 1,
           'pan_gst_hash' =>  $attributes['pan_no'][$i], 
           'status' => 1,
           'parent_pan_gst_id' =>0,    
           'biz_pan_gst_api_id' => $res->biz_pan_gst_api_id,
           'created_by' =>  Auth::user()->user_id]);
        }
        if($bizPanRes->biz_pan_gst_id > 0){
           if($i < $updateCount)
           {
             $biz_owner_id =   $attributes['ownerid'][$i];
             $ownerInputArr =  BizOwner::where('biz_owner_id',$attributes['ownerid'][$i])->update( ['biz_id' => $biz_id,   
            'user_id' => $userId, 
            'first_name' => $attributes['first_name'][$i],
            'is_promoter' => isset($attributes['is_promoter'][$i]) ? $attributes['is_promoter'][$i] : 0,
            'mobile_no' => $attributes['mobile_no'][$i],     
            'date_of_birth' => ($attributes['date_of_birth'])? Carbon::createFromFormat('d/m/Y', $attributes['date_of_birth'][$i])->format('Y-m-d'): NULL,
            'gender' => $attributes['gender'][$i],
            'owner_addr' => $attributes['owner_addr'][$i],
            'is_pan_verified' => 1, 
            'biz_pan_gst_id' => $bizPanRes->biz_pan_gst_id,	
            'share_per' => $attributes['share_per'][$i],
            'edu_qualification' => $attributes['edu_qualification'][$i],
            'other_ownership' => $attributes['other_ownership'][$i],
            'networth' => $attributes['networth'][$i],
            'created_by' =>  Auth::user()->user_id]);
              
           }
      else {
            $ownerInputArr =  BizOwner::create( ['biz_id' => $biz_id,   
            'user_id' => $userId, 
            'first_name' => $attributes['first_name'][$i],
            'is_promoter' => isset($attributes['is_promoter'][$i]) ? $attributes['is_promoter'][$i] : 0,
            'mobile_no' => $attributes['mobile_no'][$i], 
            'date_of_birth' => ($attributes['date_of_birth'])? Carbon::createFromFormat('d/m/Y', $attributes['date_of_birth'][$i])->format('Y-m-d'): NULL,
            'gender' => $attributes['gender'][$i],
            'owner_addr' => $attributes['owner_addr'][$i],
            'is_pan_verified' => 1, 
            'biz_pan_gst_id' => $bizPanRes->biz_pan_gst_id,	
            'share_per' => $attributes['share_per'][$i],
            'edu_qualification' => $attributes['edu_qualification'][$i],
            'other_ownership' => $attributes['other_ownership'][$i],
            'networth' => $attributes['networth'][$i],
            'created_by' =>  Auth::user()->user_id]);
            $biz_owner_id  = $ownerInputArr->biz_owner_id;
          }
        }
        
        if($biz_owner_id > 0){
             $ownerUpdate =  BizPanGst::where('biz_pan_gst_id',$bizPanRes->biz_pan_gst_id)
                            ->update(['biz_owner_id' => $biz_owner_id ]);
        }
     }

     return $ownerUpdate;
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
     {  /* save response api data */
         
         $res = BizPanGstApi::create(['file_name' => $attributes['response'][$i],
         'created_at' => $dateTime,
         'created_by' => Auth::user()->user_id]); 
//         dd($attributes);
         /* save Owner api data */
        if($res->biz_pan_gst_api_id > 0){
            $bizPanRes =  BizPanGst::create( [   
           'user_id' => $userId, 
           'biz_id' => $biz_id,    
           'type' => 1,
           'pan_gst_hash' =>  $attributes['pan_no'][$i], 
           'status' => 1,
           'parent_pan_gst_id' =>0,    
           'biz_pan_gst_api_id' => $res->biz_pan_gst_api_id,
           'created_by' =>  Auth::user()->user_id]);
        }
        if($bizPanRes->biz_pan_gst_id > 0){
          
            $ownerInputArr =  BizOwner::create( ['biz_id' => $biz_id,   
            'user_id' => $userId, 
            'first_name' => $attributes['first_name'][$i],
            'is_promoter' => isset($attributes['is_promoter'][$i]) ? $attributes['is_promoter'][$i] : Null,
            'mobile_no' => $attributes['mobile_no'][$i], 
            'date_of_birth' =>($attributes['date_of_birth'])? Carbon::createFromFormat('d/m/Y', $attributes['date_of_birth'][$i])->format('Y-m-d'): NULL,
            'gender' => $attributes['gender'][$i],
            'owner_addr' => $attributes['owner_addr'][$i],
            'is_pan_verified' => 1, 
            'biz_pan_gst_id' => $bizPanRes->biz_pan_gst_id,	
            'share_per' => $attributes['share_per'][$i],
            'edu_qualification' => $attributes['edu_qualification'][$i],
            'other_ownership' => $attributes['other_ownership'][$i],
            'networth' => $attributes['networth'][$i],
            'created_by' =>  Auth::user()->user_id]);
            $biz_owner_id  = $ownerInputArr->biz_owner_id;
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
        $arrData = self::select('biz_owner.first_name','biz_owner.biz_owner_id','biz_owner.last_name','biz_pan_gst.pan_gst_hash', 'biz_owner.email','biz_owner.mobile_no','biz_owner.cibil_score', 'biz_owner.is_cibil_pulled')
        ->leftjoin('biz_pan_gst', 'biz_pan_gst.biz_pan_gst_id', '=', 'biz_owner.biz_pan_gst_id')
        ->where('biz_owner.biz_id', $biz_id)
        ->get();
        return $arrData;
    }

 
  public static function getBizOwnerDataByOwnerId($biz_owner_id)
  {
     $arrData = self::select('biz_owner.*','biz_pan_gst.pan_gst_hash')
        ->join('biz_pan_gst', 'biz_pan_gst.biz_pan_gst_id', '=', 'biz_owner.biz_pan_gst_id')
        ->where('biz_owner.biz_owner_id', $biz_owner_id)
        ->first();
        return $arrData;
  }



   
}