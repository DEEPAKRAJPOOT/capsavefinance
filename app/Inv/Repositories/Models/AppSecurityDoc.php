<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Notifications\Notifiable;
use App\Inv\Repositories\Factory\Models\BaseModel;
use Helpers;
use Auth;
use DB;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

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
        'cam_reviewer_summary_id',
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
        'extended_due_date',
        'maturity_date',
        'renewal_reminder_days',
        'renewal_reminder_date',
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

    public static function getAllAppSecurityDoc($renewalReminderDate){
        if (empty($renewalReminderDate)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        $appSecData =  self::select('a.app_id','u.user_id','u.email','mst_sec.name as doc_type_name','app_security_doc.*','u.f_name','u.l_name')
        ->join('app as a', 'app_security_doc.app_id', '=', 'a.app_id')
        ->join('users as u', 'a.user_id', '=', 'u.user_id')
        ->join('mst_security_doc as mst_sec', 'app_security_doc.security_doc_id', '=', 'mst_sec.security_doc_id')
        ->whereDate('app_security_doc.renewal_reminder_date', $renewalReminderDate)
        ->where('app_security_doc.is_active', 1)  
        ->where('a.is_assigned', 1) 
        ->where('u.is_active', 1)       
        ->where('app_security_doc.completed', 'no')       
        ->get();
        return $appSecData;
    }

    public function mstSecurityDocs()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\Master\SecurityDocument','security_doc_id','security_doc_id')->where('is_active',1);
    }

    public function createdByUser()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\User','created_by','user_id')->where('is_active',1);
    }

    public function currentStatusId()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\Application','app_id','app_id')->where('is_active',1);
    }

}



