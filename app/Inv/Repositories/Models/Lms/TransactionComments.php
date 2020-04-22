<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class TransactionComments extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'transactions_comments';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'trans_comment_id';

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
        'trans_id',
        'comment',
        'created_by',
        'created_at',
    ];

    /**
     * Save Invoices
     * 
     * @param array $invoices
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function saveTxnComments($comments,$whereCondition=[])
    {
        if (!is_array($comments)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        if (!isset($comments['created_at'])) {
            $comments['created_at'] = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
        }
        if (!isset($comments['created_by'])) {
            $comments['created_by'] = \Auth::user()->user_id;
        }        
        
        if (!empty($whereCondition)) {
            return self::where($whereCondition)->update($comments);
        } else if (!isset($comments[0])) {
            return self::create($comments);
        } else {            
            return self::insert($comments);
        }
    }

    /**
     * Get Invoices
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getTxnComments($whereCondition=[]) {
        if (!is_array($whereCondition)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        $query = self::select('*');    
        if (!empty($whereCondition)) {
            $query->where($whereCondition);
        }
        $result = $query->get();
        return $result;
    }
}
