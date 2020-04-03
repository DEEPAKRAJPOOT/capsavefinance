<?php
namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;
use App\Inv\Repositories\Models\Lms\TransType;
use App\Inv\Repositories\Models\Master\Voucher;
use Auth;
use Illuminate\Http\Request;
use Session;
use Carbon\Carbon;

class VoucherController extends Controller {
	/**
	 * The pdf instance.
	 *
	 * @var App\Libraries\Pdf
	 */
	
	public function __construct(){
		$this->middleware('checkBackendLeadAccess');
	}
	
    public function index()  {
    	return view('master.vouchers.index');
    }

    public function addVoucher()  {
    	$transType = TransType::where('is_active', 1)->orderBy('trans_name')->get();
    	return view('master.vouchers.add_voucher', ['transType' => $transType]);
    }

    public function saveVoucher(Request $request)  {
    	$allData = $request->all();
    	$resp = [
    		'status' => 'error',
    		'message' => 'All fields are required.',
    	];
    	if (empty($allData['trans_type_id'])) {
    		$resp["message"] = 'Transaction Type is required';
    	} else if (empty(preg_replace('#[^A-Za-z.\s]+#', '', $allData['voucher_name']))) {
    		$resp["message"] = 'Valid Voucher Name is required';
    	}else{
    		$resp = Voucher::saveVoucher($allData);
    	}
    	if ($resp['status'] == 'success') {
    		$msgType = "message";
    		$message = $resp["message"];
    	}else{
    		$msgType = "error";
    		$message = $resp["message"];
    	}
    	return back()->with($msgType, $message);	
    }
}