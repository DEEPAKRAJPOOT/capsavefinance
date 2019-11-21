<?php
namespace App\Inv\Repositories\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Auth;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use App\Inv\Repositories\Models\AppDocument;
use App\Inv\Repositories\Models\BizPanGstApi;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\BizPanGst;

class BizOwner extends Model
{
    use Notifiable;
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
        'last_name',
        'date_of_birth',
        'gender',
        'share_per',
        'edu_qualification',
        'other_ownership',
        'networth',
        'owner_addr',
        'created_by',
        'created_at',
        'updated_at'
    
    ];
    /* get owner api details */
    /* created by gajendra chauhan   */
   public static function getOwnerApiDetails($bizId)
   {
      $biz_id = $bizId['biz_id'];
      return BizOwner::with('pan')->where('biz_id', $biz_id)->get();
   }
    /* Relation of Owner and Gst Api relation*/
    /* created by gajendra chauhan   */
   public function pan()
   {
      return $this->belongsTo('App\Inv\Repositories\Models\BizPanGst', 'biz_pan_gst_id','biz_pan_gst_id')->where(['type' => 1]);  
       
   }
   
/* save biz owner data*/
    /* By gajendra chauhan  */  
   public static function createsOwner($attributes)
    { 
        $uid = Auth::user()->user_id;
        $appData = self::getAppId($uid);
       foreach($attributes as $key=>$val)
       {
           
                $ownerInputArr =  BizOwner::create( ['biz_id' =>4,   
                'user_id' => $uid, 
                'first_name' => $val['first_name'],
                'date_of_birth' => date('Y-m-d', strtotime($val['dob'])),
                'owner_addr' => $val['address'],
                'created_by' =>  $uid]);
       }      
       return  $ownerInputArr;
   }
    public static function creates($attributes)
    {
          //insert into rta_app_doc
          $uid = Auth::user()->user_id;
          /* Get App id and biz id behalf of user id */
          $appData = self::getAppId($uid);
          $getRes =  self::savePanApiRes($attributes,$appData->biz_id); 
          $owner = AppDocument::insert([
            [
            'rcu_status' => 0,
            'user_id' => $uid,
            'app_id' => (int) $attributes['app_id'],
            'doc_id' => 4,
            'is_upload' => 0,
            'created_by' => $uid,
            'updated_by' => $uid
            ],
            [
            'rcu_status' => 0,
            'user_id' => $uid,
            'app_id' => (int) $attributes['app_id'],
            'doc_id' => 5,
            'is_upload' => 0,
            'created_by' => $uid,
            'updated_by' => $uid
            ],
            [
            'rcu_status' => 0,
            'user_id' => $uid,
            'app_id' => (int) $attributes['app_id'],
            'doc_id' => 6,
            'is_upload' => 0,
            'created_by' => $uid,
            'updated_by' => $uid
            ]
            ]);
         
          return $owner;

    }
  
    /*  Save Pan Api Response data //////////////////  */
  public static function savePanApiRes($attributes,$biz_id)
  {
      
    $count = count($attributes['response']);
    $userId  = Auth::user()->user_id;
    $mytime = Carbon::now();
    $dateTime = $mytime->toDateTimeString();
     for ($i=0;$i<$count;$i++) 
     {  /* save response api data */
         
         $res = BizPanGstApi::create(['file_name' => $attributes['response'][$i],
         'created_at' => $dateTime,
         'created_by' => $userId]); 
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
           'created_by' =>  $userId]);
          
        }
        if($bizPanRes->biz_pan_gst_id > 0){
          
            $ownerInputArr =  BizOwner::create( ['biz_id' => $biz_id,   
           'user_id' => $userId, 
           'first_name' => $attributes['first_name'][$i],
           'last_name' => $attributes['last_name'][$i],
           'date_of_birth' => date('Y-m-d', strtotime($attributes['date_of_birth'][$i])),
           'gender' => $attributes['gender'][$i],
           'owner_addr' => $attributes['owner_addr'][$i],
           'is_pan_verified' => 1, 
           'biz_pan_gst_id' => $bizPanRes->biz_pan_gst_id,	
           'share_per' => $attributes['share_per'][$i],
           'edu_qualification' => $attributes['edu_qualification'][$i],
           'other_ownership' => $attributes['other_ownership'][$i],
           'networth' => $attributes['networth'][$i],
           'created_by' =>  $userId]);
        }
        if($ownerInputArr->biz_owner_id > 0){
             $ownerUpdate =  BizPanGst::where('biz_pan_gst_id',$bizPanRes->biz_pan_gst_id)
                            ->update(['biz_owner_id' => $ownerInputArr->biz_owner_id]);
        }
     }

     return $ownerUpdate;
  }
  
  public static function getAppId($uid)
  {
      $res =  Application::where(['status' => 0,'user_id' => $uid])->first();
      return $res;
  }

  public static function getCompanyOwnerByBizId($biz_id)
    {
        $arrData = self::select('biz_owner.first_name','biz_owner.last_name','biz_pan_gst.pan_gst_hash')
        ->join('biz_pan_gst', 'biz_pan_gst.biz_pan_gst_id', '=', 'biz_owner.biz_pan_gst_id')
        ->where('biz_owner.biz_id', $biz_id)
        ->get();
        return $arrData;
    }
   
}