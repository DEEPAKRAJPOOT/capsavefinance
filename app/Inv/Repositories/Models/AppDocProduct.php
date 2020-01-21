<?php

namespace App\Inv\Repositories\Models;

use DB;
use File;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Inv\Repositories\Factory\Models\BaseModel;

class AppDocProduct extends BaseModel
{
 

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'app_doc_product';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'app_doc_product_id';
    
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
    public $userstamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'app_doc_id',
        'doc_id',
        'product_id',
        'is_active',
        'created_at',
        'updated_at',
     ];
    
    public function product(){
        return $this->belongsTo('App\Inv\Repositories\Models\Master\Product', 'product_id')->where('is_active', 1);
    }
}
  

