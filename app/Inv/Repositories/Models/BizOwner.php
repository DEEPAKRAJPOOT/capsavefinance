<?php
namespace App\Inv\Repositories\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Auth;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use App\Inv\Repositories\Models\BizPanGstApi;

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
        'owner_addr',
        'created_by',
        'created_at',
        'updated_at'
    
    ];
//////////////// save biz owner data///////////////////
    public static function creates($attributes)
    {
          $getRes =  BizOwner::savePanApiRes($attributes);
          return $getRes;
    }
  /*  Save input array data //////////////////  */
  public static function arrayInputData($attributes, $bizPanGstId)
  {
     $inputArr = [];
     $count = count($attributes['first_name']);
     $userId  = Auth::user()->user_id;
     for ($i=0;$i<$count;$i++) 
     {
         $date = Carbon::createFromFormat('d/m/Y', $attributes['date_of_birth'][$i]);
         $inputArr['biz_id']  = 1;   
         $inputArr['user_id']  = $userId; 
         $inputArr['first_name'] = $attributes['first_name'][$i];
         $inputArr['last_name'] = $attributes['last_name'][$i];
         $inputArr['date_of_birth'] = $date;
         $inputArr['gender'] = $attributes['gender'][$i];
         $inputArr['owner_addr'] = $attributes['owner_addr'][$i];
         $inputArr['is_pan_verified'] = 1; 
         $inputArr['biz_pan_gst_id'] = $bizPanGstId;	
         $inputArr['share_per'] = $attributes['share_per'][$i];
         $inputArr['edu_qualification'] = $attributes['edu_qualification'][$i];
         $inputArr['created_by'] =  $userId;
     }
     return $inputArr;
  }
    /*  Save Pan Api Response data //////////////////  */
  public static function savePanApiRes($attributes)
  {
    $inputArr = [];
    $ownerInputArr=[];
    $count = count($attributes['response']);
    $userId  = Auth::user()->user_id;
    $mytime = Carbon::now();
    $dateTime = $mytime->toDateTimeString();
     for ($i=0;$i<$count;$i++) 
     {  /* save response api data */
         $inputArr['file_name'] = $attributes['response'][$i];
         $inputArr['created_at'] = $dateTime;
         $inputArr['created_by'] = $userId;
         $res = BizPanGstApi::create($inputArr); 
         /* save Owner api data */
        if($res->biz_pan_gst_api_id > 0){
            $date = Carbon::createFromFormat('d/m/Y', $attributes['date_of_birth'][$i]);
           $ownerInputArr[$i]['biz_id']  = 1;   
           $ownerInputArr[$i]['user_id']  = $userId; 
           $ownerInputArr[$i]['first_name'] = $attributes['first_name'][$i];
           $ownerInputArr[$i]['last_name'] = $attributes['last_name'][$i];
           $ownerInputArr[$i]['date_of_birth'] = $date;
           $ownerInputArr[$i]['gender'] = $attributes['gender'][$i];
           $ownerInputArr[$i]['owner_addr'] = $attributes['owner_addr'][$i];
           $ownerInputArr[$i]['is_pan_verified'] = 1; 
           $ownerInputArr[$i]['biz_pan_gst_id'] =$res->biz_pan_gst_api_id;	
           $ownerInputArr[$i]['share_per'] = $attributes['share_per'][$i];
           $ownerInputArr[$i]['edu_qualification'] = $attributes['edu_qualification'][$i];
           $ownerInputArr[$i]['created_by'] =  $userId;
         }
     }
     $ownerInputArr =  BizOwner::insert($ownerInputArr);
     return $ownerInputArr;
  }
   
}