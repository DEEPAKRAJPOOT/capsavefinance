<?php
namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;
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
}