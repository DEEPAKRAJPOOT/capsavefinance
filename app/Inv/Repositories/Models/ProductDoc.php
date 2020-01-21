<?php

namespace App\Inv\Repositories\Models;

use DB;
use File;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Inv\Repositories\Factory\Models\BaseModel;

class ProductDoc extends BaseModel
{
 

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'product_doc';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'product_doc_id';
    
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
        'product_id',
        'doc_id',
        'is_active',
        'created_at',
        'updated_at',
     ];
    
    public function product(){
        return $this->belongsTo('App\Inv\Repositories\Models\Master\Product', 'product_id')->where('is_active', 1);
    }
}
  

