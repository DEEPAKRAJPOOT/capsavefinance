<?php
namespace App\Http\Controllers\Lms;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use App\Libraries\Idfc_lib;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use App\Inv\Repositories\Contracts\InvoiceInterface as InvoiceInterface;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use Session;
use Carbon\Carbon;
use DB;
use Helpers;
use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;
use App\Inv\Repositories\Contracts\Traits\LmsTrait;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
class ChargeController extends Controller
{
	use ApplicationTrait;
	use LmsTrait;
        protected $invRepo;
	protected $appRepo;
	protected $userRepo;
	protected $docRepo;
	protected $lmsRepo;
	protected $masterRepo;

	/**
	 * The pdf instance.
	 *
	 * @var App\Libraries\Pdf
	 */
	protected $pdf;
	
	public function __construct(InvoiceInterface $invRepo,InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo, InvLmsRepoInterface $lms_repo ,InvMasterRepoInterface $master){
	        $this->invRepo = $invRepo;	
                $this->appRepo = $app_repo;
		$this->userRepo = $user_repo;
		$this->docRepo = $doc_repo;
		$this->lmsRepo = $lms_repo;
                $this->masterRepo = $master;
		$this->middleware('checkBackendLeadAccess');
	}
	
	/**
	 * Display a listing of the customer.
	 *
	 * @return \Illuminate\Http\Response
	 */
	 public function manageCharge(){
             return view('lms.charges.manage_charge');
        }

    
     public function editLmsCharges(){
        return view('lms.charges.edit_charges');
      }
    
     public function listLmsCharges(){
          $res  =  $this->lmsRepo->getTrnasType(['is_active' => 1]);
          $result  =  $this->invRepo->getCustomerId();
          $program  =  $this->lmsRepo->getProgram();
          return view('lms.charges.add_charges')->with(['transtype' => $res,'customer' =>$result,'program' => $program]);
      }
      
      public function  getChrgAmount(Request $request)
      {
          $res =  $request->all();
          $getamount  =   $this->lmsRepo->getSingleChargeAmount($res);
          if($getamount)
          {
          
              return response()->json(['status' => 1,'amount' => number_format($getamount->chrg_calculation_amt),'id' => $getamount->id]); 
          }
          else
          {
              return response()->json(['status' => 0]); 
          }
          
      }
      
       public function saveManualCharges(Request $request)
       {
            $getTransType  =  DB::table('mst_trans_type')->where(['is_charge' => $request['id']])->first();
           if($getTransType)
           {
                    $id  = Auth::user()->user_id;
                    $mytime = Carbon::now(); 
                    $amount = str_replace(',', '.', $request->amount);
                    $arr  = ["user_id" =>  $request->user_id,
                    "prgm_id" => $request->program_id,
                    "charge_id" => $request->chrg_name,
                    "amount" =>   $amount,
                    "trans_date" => ($request['charge_date']) ? Carbon::createFromFormat('d/m/Y', $request['charge_date'])->format('Y-m-d') : '',
                    "trans_type" => $getTransType->id,
                    'created_by' =>  $id,
                    'created_at' =>  $mytime ];
                    
                  $res =   $this->lmsRepo->saveCharge($arr);
                  if($res)
                  {
                          Session::flash('message', 'Data has been saved');
                           return redirect('lms/charges/manage_charge'); 
                  }
                  else
                  {
                          Session::flash('message', 'Something went wrong, Please try again');
                          return redirect('lms/charges/manage_charge'); 
                  }
                   
           }
                else {
                         Session::flash('message', 'Something went wrong, Please try again');
                         return redirect('lms/charges/manage_charge'); 
                }
        
       }
      
      
}