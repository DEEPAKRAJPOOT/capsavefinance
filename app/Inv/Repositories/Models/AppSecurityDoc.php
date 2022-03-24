<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Notifications\Notifiable;
use App\Inv\Repositories\Factory\Models\BaseModel;
use Helpers;
use Auth;

class AppSecurityDoc extends BaseModel
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'app_security_doc';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'app_security_doc_id';

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
        'app_id',
        'security_doc_id',
        'description',
        'document_number',
        'due_date',
        'completed',
        'exception_received',
        'exception_received_from',
        'exception_received_date',
        'exception_remark',
        'maturity_date',
        'renewal_reminder_days',
        'amount_expected',
        'document_amount',
        'doc_type',
        'is_upload',
        'file_id',
        'is_active',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    public static function saveAppSecurityDoc($attributes){
        $response = self::create($attributes);
        return  $response ? true : false;

    }

}



