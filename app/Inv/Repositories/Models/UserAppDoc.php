<?php

namespace App\Inv\Repositories\Models;
use App\Inv\Repositories\Factory\Models\BaseModel;

class UserAppDoc extends BaseModel
{
 

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_app_doc';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'user_doc_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'app_id',
        'file_id',
        'product_id',
        'file_type',
        'created_by',
        'updated_by'
     ];
  
     public static function getLatestDoc($appId, $productId, $fileType)
     {
         $outQry = self::select('file.file_id','file.file_path','file.file_name')
                 ->join('file', 'file.file_id', '=', 'user_app_doc.file_id')                
                 ->where('user_app_doc.app_id', $appId)
                 ->where('user_app_doc.product_id', $productId)
                 ->where('user_app_doc.file_type', $fileType)
                 ->where('user_app_doc.is_active', 1)
                 ->where('file.is_active', 1)
                 ->first();   
         return  ($outQry) ? $outQry->toArray() : false;   
     }
}
  

