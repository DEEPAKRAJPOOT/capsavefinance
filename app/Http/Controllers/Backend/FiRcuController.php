<?php

namespace App\Http\Controllers\Backend;

use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Helpers;
use Auth;

class FiRcuController extends Controller
{
    protected $appRepo;
    protected $userRepo;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo){
        $this->appRepo = $app_repo;
        $this->userRepo = $user_repo;
        $this->middleware('checkBackendLeadAccess');
    }
    
    /**
     * Display a listing of FI
     */
    public function listFI()
    {
       return view('backend.fircu.fi');   
    }

    /**
     * Display a listing of RCU
     */
    public function listRCU()
    {
       return view('backend.fircu.rcu');   
    }

}
