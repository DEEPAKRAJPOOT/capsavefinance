<?php
namespace App\Http\Controllers\Lms;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use Session;
use Helpers;
use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;

class CustomerController extends Controller
{
	use ApplicationTrait;
	
	protected $appRepo;
	protected $userRepo;
	protected $docRepo;

	/**
	 * The pdf instance.
	 *
	 * @var App\Libraries\Pdf
	 */
	protected $pdf;
	
	public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo){
		$this->appRepo = $app_repo;
		$this->userRepo = $user_repo;
		$this->docRepo = $doc_repo;
		$this->middleware('checkBackendLeadAccess');
	}
	
	/**
	 * Display a listing of the customer.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function list()
	{
	   	return view('lms.customer.list');              
	}

	/**
	 * Display a listing of the customer.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function listAppliction(Request $request)
	{
		try {
            $user_id = $request->get('user_id');
            $userInfo = $this->userRepo->getUserDetail($user_id);
            $application = $this->appRepo->getCustomerApplications($user_id)->toArray();
            // dd($application);
            
            return view('lms.customer.list_applications')
			  	->with('userInfo', $userInfo)
			  	->with('application', $application);

        } catch (Exception $ex) {
            dd($ex);
        }

	}

}