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

    /**
     * Pan status verification API
     *
     * @return \Illuminate\Http\Response
     */
    public function checkPanStatusVerification(KarzaApi $KarzaApi, Request $request)
    {
          $requestPan   = $request->all();
          return $KarzaApi->checkPanStatusVerification($requestPan);
    }
    
    
    /**
     * Voter ID Card Verification status verification API
     *
     * @return \Illuminate\Http\Response
     */
    public function checkVoterIdVerification(KarzaApi $KarzaApi, Request $request)
    {
          $requestPan   = $request->all();
          return $KarzaApi->checkVoterIdVerification($requestPan['epic_no']);
    }
    
       /**
     * Voter ID Card Verification status verification API
     *
     * @return \Illuminate\Http\Response
     */
    public function checkDlVerification(KarzaApi $KarzaApi, Request $request)
    {
          $requestDl   = $request->all();
          return $KarzaApi->checkDlVerification($requestDl);
    }
    
    /**
     * Passport ID Card Verification status verification API
     *
     * @return \Illuminate\Http\Response
     */
    public function checkPassportVerification(KarzaApi $KarzaApi, Request $request)
    {
          $requestPassport   = $request->all();
          return $KarzaApi->checkPassportVerification($requestPassport);
    }
}