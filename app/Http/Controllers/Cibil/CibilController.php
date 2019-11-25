<?php
 
namespace App\Http\Controllers\Cibil;

use App\Http\Controllers\Controller;
use App\Libraries\Ui\CibilApi;
use Auth;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use Session;
use File;
 
class CibilController extends Controller
{
    protected $appRepo;
    protected $userRepo;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, CibilApi $CibilApi){
        $this->userRepo = $user_repo;
        $this->appRepo = $app_repo;
     }
    
    /**
     * Show the business information form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPromoterCibilRequest(CibilApi $CibilApi, Request $request)
    {      
         $responce =  $CibilApi->getPromoterCibilRequest();
         $file_name = 'cibil.txt';
         File::put(storage_path('app/public/cibil').'/'.$file_name, $responce);
         dd($responce);
         return $responce;
    }

    
}