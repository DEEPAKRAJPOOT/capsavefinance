<?php
namespace App\Inv\Repositories\Models\Lms;

use App\Inv\Repositories\Factory\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class OverdueReportLog extends BaseModel {

    use SoftDeletes;
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'overdue_report_logs';

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
    public $timestamps = true;

    /**
     * Maintain created_by and updated_by automatically
     *
     * @var boolean
     */
    public $userstamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'to_date',
        'file_path',
        'created_at',
        'created_by',
        'deleted_at',
    ];


    protected $casts = [
        'to_date' => 'date:Y-m-d'
    ];
    
    public function user()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\User','user_id','user_id');
    }
    
    public function lmsUser()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\LmsUser','user_id','user_id');
    }

    public function createdByUser()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\User','created_by','user_id');
    }

    public function getCreatedByUserNameAttribute(){
        if($this->created_by > 0){
            return $this->createdByUser->full_name;
        }else{
            return 'System';
        }
    }
}