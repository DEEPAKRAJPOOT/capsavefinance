<?php
namespace App\Inv\Repositories\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Notifications\Notifiable;

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
          $inputArr =  BizOwner::arrayInputData($attributes);
          $owner = BizOwner::insert($inputArr);
          return $owner;
    }
  /*  Save input array data //////////////////  */
  public static function arrayInputData($attributes)
  {
     $inputArr = [];
     $count = count($attributes['first_name']);
     for ($i=0;$i<$count;$i++) 
     {
         $inputArr[$i]['biz_id']  = 1;   
         $inputArr[$i]['user_id']  = 1; 
         $inputArr[$i]['first_name'] = $attributes['first_name'][$i];
         $inputArr[$i]['last_name'] = $attributes['last_name'][$i];
         $inputArr[$i]['date_of_birth'] = $attributes['date_of_birth'][$i];
         $inputArr[$i]['gender'] = $attributes['gender'][$i];
         $inputArr[$i]['owner_addr'] = $attributes['owner_addr'][$i];
         $inputArr[$i]['biz_pan_id'] = 1;
         $inputArr[$i]['is_pan_verified'] = 1; 
         $inputArr[$i]['share_per'] = $attributes['share_per'][$i];
         $inputArr[$i]['edu_qualification'] = $attributes['edu_qualification'][$i];
         $inputArr[$i]['created_by'] = 1;
     }
     return $inputArr;
  }
   
}