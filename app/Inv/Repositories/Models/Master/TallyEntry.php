<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use Session;
use Auth;
use DB;
 

class TallyEntry extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tally_entry';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'tally_entry_id';

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
        'batch_no',
        'transactions_id',
        'voucher_no',
        'voucher_type',
        'voucher_date',
        'is_debit_credit',
        'trans_type',
        'invoice_no',
        'invoice_date',
        'ledger_name',
        'amount',
        'ref_no',
        'ref_amount',
        'acc_no',
        'ifsc_code',
        'bank_name',
        'cheque_amount',
        'cross_using',
        'mode_of_pay',
        'inst_no',
        'inst_date',
        'favoring_name',
        'remarks',
        'generated_by',
        'remarks',
        'narration',
        'created_at',
    ];

    /**
     * get Tally Data
     * 
     * @param type $where array
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public static function getTallyData(array $where = []) {
        if (empty($where)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        if (!is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        $res = self::where($where)->get();
        return $res ?: false;
    }
    
    public static function saveTallyData(array $attributes = []) {
        $created_at = \carbon\Carbon::now();
        $uid = Auth::user()->user_id;
        $resp = [
            'status' => 'success',
            'code' => '000',
            'message' => 'success',
        ];
         try {
            $insertId = self::create($attributes)->tally_entry_id;
            $resp['code'] = $insertId;  
            $resp['message'] = 'Tally inserted successfuly';  
        } catch (\Exception $e) {
            $errorInfo  = $e->errorInfo;
            $resp['status'] = 'error';  
            $resp['code'] = $errorInfo[1] ?? '444';  
            $resp['message'] = preg_replace('#[^A-Za-z./\s\_]+#', '', $errorInfo[2]) ?? 'Some DB Error occured. Try again.';  
        }
        return $resp;
      }

      public static function getActualPostedAmount() {
          return self::select('is_debit_credit', DB::raw("SUM(amount) as amount"))->where('generated_by', '=', '0')->groupBy('is_debit_credit')->get();
      }
    
    
}
