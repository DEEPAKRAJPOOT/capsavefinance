<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use File;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Inv\Repositories\Factory\Models\BaseModel;

class PaymentApportionment extends BaseModel
{
 

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payment_apportionment';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'payment_aporti_id';

    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'payment_id',
        'file_id',
        'parent_id',
        'status',
        'is_active'
     ];
    
    
    /**
    * Create a new record in self
    *
    * @param Array $attributes
    *
    * @return Array
    */
    
    public static function creates($attributes, $type)
    {
        if($attributes['payment_id']){
            $query = self::where('payment_id', $attributes['payment_id']);
            if($type == 'upload'){
                $query->where('parent_id', $attributes['parent_id']);
            }
            $query->where('status', 1);
            $query->update(['is_active' => 0]);
            $file = self::create($attributes);
            return $file;
        }
        return false;
    }

    public static function getLastPaymentAportData($attributes){
        $res = self::select('payment_apportionment.*','file.file_name')
        ->join('file', 'file.file_id', '=', 'payment_apportionment.file_id')
        ->where(['payment_apportionment.payment_id'=>$attributes['payment_id'],'payment_apportionment.parent_id'=>0,'payment_apportionment.status'=>1,'payment_apportionment.is_active'=>1])
        ->orderBy('payment_aporti_id','DESC')
        ->first();
        return ($res);
    }

    public static function checkApportionmentHold($user_id, $payment_id= null)
    {
        $res = self::select('payment_apportionment.*','file.file_name')
        ->join('file', 'file.file_id', '=', 'payment_apportionment.file_id')
        ->join('payments', 'payments.payment_id', '=', 'payment_apportionment.payment_id');
        if($payment_id){
            $res->where('payment_apportionment.payment_id', $payment_id);  
        }
        $res = $res->whereIn('payments.is_settled', [0,2,3])
        ->where('payment_apportionment.user_id', $user_id)
        ->where('payment_apportionment.parent_id', 0)
        ->where('payment_apportionment.is_active', 1)
        ->first();
        return ($res);
    }

    public function file()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\UserFile', 'file_id', 'file_id');
    }
}
  

