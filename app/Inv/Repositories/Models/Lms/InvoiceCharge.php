<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use Carbon\Carbon;
use App\Inv\Repositories\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class InvoiceCharge extends BaseModel {
	
    // use SoftDeletes;

	protected $table = 'invoice_chrg';

	protected $primaryKey = 'invoice_chrg_id';

	// protected $softDelete = true;
    
    public $timestamps = true;
 
    public $userstamps = true;
 
	protected $fillable = [
		'invoice_id',
		'charge_id',
		'chrg_type',
		'chrg_value',
		'gst_chrg_value',
		'deductable',
		'is_active',
		'created_at',
		'created_by',
		'updated_at',
		'updated_by',
		// 'deleted_at',
	];

	/**
	 * Save or Update
	 * 
	 * @param array $data
	 * @param array $whereCondition | optional
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public static function saveUpdateInvoiceCharge($data, $whereCondition=[])
	{
		if (!is_array($data)) {
			throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
		}
		
		if (!empty($whereCondition)) {
			return self::where($whereCondition)->update($data);
		} else if (isset($data[0])) {
			return self::insert($data);
		} else {
			return self::create($data);
		}
	}
 
	public function charge() { 
		return $this->belongsTo('App\Inv\Repositories\Models\Master\Charges', 'charge_id', 'id'); 
	}

	public function invoice(){
		return $this->belongsTo('App\Inv\Repositories\Models\BizInvoice','invoice_id','invoice_id');
	}
 	
 	public static function updateInvoiceCharge($data, $invoiceId)
    {

        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        $obj = self::firstOrNew(['invoice_id' => $invoiceId]);
        $obj->fill($data)->save();

        return $obj;
        
    }   

       
}
