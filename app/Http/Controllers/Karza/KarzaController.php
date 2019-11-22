<?php
 
namespace App\Http\Controllers\Karza;

use App\Http\Controllers\Controller;
use App\Libraries\Ui\KarzaApi;
use Auth;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use Session;
 
class KarzaController extends Controller
{
    protected $appRepo;
    protected $userRepo;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo){
        $this->userRepo = $user_repo;
        $this->appRepo = $app_repo;
    }
    
    /**
     * Show the business information form.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkPanVerification(KarzaApi $KarzaApi, Request $request)
    {
          $requestPan   = $request->all();
          return $KarzaApi->panVerificationRequest($requestPan['pan']);
    }

    
}