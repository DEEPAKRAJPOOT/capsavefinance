<?php

namespace App\Inv\Repositories\Models\Lms;

use App\Inv\Repositories\Factory\Models\BaseModel;

class ChargeTransactionDeleteLog extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
   protected $table = 'chrg_transaction_delete_logs';

   /**
    * Custom primary key is set for the table
    *
    * @var integer
    */
   protected $primaryKey = 'id';

   /**
    * Maintain created_at and updated_at automatically
    *
    * @var boolean
    */

   public $timestamps = false;


   /**
    * Maintain created_by and updated_by automatically
    *
    * @var boolean
    */
   public $userstamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
   protected $fillable = [
      'chrg_trans_id',
      'status',
      'created_at',
      'created_by'
   ];
    
   public static function saveChargeTransDeleteLog($attr)
   {
      return self::create($attr);
   }
    
   public function chrgTransaction()
   {
      return $this->belongsTo('App\Inv\Repositories\Models\Lms\ChargesTransactions','chrg_trans_id','chrg_trans_id');   
   }

   public function scopeReqForDeletion($query)
   {
      return $query->where('status', 1);   
   }
   
   public function scopeApproveForDeletion($query)
   {
      return $query->where('status', 2);   
   }
}
