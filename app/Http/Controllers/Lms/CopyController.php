<?php
namespace App\Http\Controllers\Lms;
use Auth;
use Session;
use Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\Idfc_lib;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;
use App\Inv\Repositories\Contracts\Traits\LmsTrait;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;

class CopyController extends Controller
{
	use ApplicationTrait;
	use LmsTrait;
       
        
	protected $appRepo;
	protected $userRepo;
	protected $docRepo;
	protected $lmsRepo;
	protected $masterRepo;
	
	public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo, InvLmsRepoInterface $lms_repo ,InvMasterRepoInterface $master){
		$this->appRepo = $app_repo;
		$this->userRepo = $user_repo;
		$this->docRepo = $doc_repo;
		$this->lmsRepo = $lms_repo;
                $this->masterRepo = $master;
		
	}
	
	public  function duplicateApp(Request $request)
        {
            $user_id =  $request->user_id;
            $biz_id =  $request->biz_id;
            $app_id =  $request->app_id;
            $biz_details  = $this->businessInformation($request); ////// Ssave business information///
            if($biz_details)
            {
               $biz_id = $this->bizPanGst($biz_details);  /* save pan gst data */ 
               $app_id  = $this->applicationSave($app_id);
               dd( $app_id);
            }
        }
        
         

}