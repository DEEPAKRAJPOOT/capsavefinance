<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class Batch extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'lms_batch';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'batch_id';

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
        'batch_id',
        'ref_code',
        'type',
        'status',        
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    public function log()
    {
        return $this->hasMany('App\Inv\Repositories\Models\Lms\BatchLog', 'batch_id','batch_id');
    }
    
    public function assign()
    {
        return $this->hasMany('App\Inv\Repositories\Models\Lms\BatchAssign', 'batch_id','batch_id');
    }

    public static function createBatch($type){

        /**
         * Check id is not blank
         */
        if (empty($type)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        if (!is_int($type)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        $batchDetails = self::create(['type'=>$type]);
        $batchDetails->update(['ref_code' => 'REF000'.$batchDetails->batch_id]);
        return $batchDetails;
    }

    public function getTypeNameAttribute(){
        $data = '';
        switch ($this->type) {
            case '1':
                $data = 'Refund';
                break;
            case '2':
                $data = 'Adjustment';
                break;
            case '3':
                $data = 'Wave Off';
                break;
        }
        return $data;
    }

    public function getAssigneeNameAttribute(){
        $assigneeData = $this->assign->find($this->assign->max('batch_assign_id'));
        if($assigneeData){
            $f_name = $assigneeData->toUser->f_name;
            $m_name = $assigneeData->toUser->m_name;
            $l_name = $assigneeData->toUser->l_name;
            return $f_name.' '.$m_name.' '.$l_name; 
        }
    }

    public function getAssignedByNameAttribute(){
        $assigneeData = $this->assign->find($this->assign->max('batch_assign_id'));
        if($assigneeData){
            $f_name = $assigneeData->fromUser->f_name;
            $m_name = $assigneeData->fromUser->m_name;
            $l_name = $assigneeData->fromUser->l_name;
            return $f_name.' '.$m_name.' '.$l_name; 
        }
    }

    public function getTotalAmountAttribute(){
        if($this->log){
            return $this->log->sum('amount');
        }
    }

    public function getStatusNameAttribute(){
        $data = '';
        switch ($this->status) {
            case '1':
                $data = 'New Request';
                break;
            case '2':
                $data = 'Deleted';
                break;
            case '3':
                $data = 'In process';
                break;
            case '4':
                $data = 'Rejected';
                break;
            case '5':
                $data = 'Approved';
                break;
            case '6':
                $data = 'Processed';
                break;
        }
        return $data;
    }

}

