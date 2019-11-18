<?php

namespace App\Http\Controllers\Backend;
use Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;

class LeadController extends Controller
{
    
     protected $userRepo;
 
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    
    public function __construct( InvUserRepoInterface $user)
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('checkBackendLeadAccess');

        $this->userRepo = $user;
         
    }

  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.lead.index');
    }

    
    /**
     * Display a listing of the resource.
     * All leads
     * @return \Illuminate\Http\Response
     */
     public function leadspool(){
         
         return view('backend.lead.leadpoll');
     }
     
     
    /**
     * Edit backend Lead
     * 
     * @param Request $request
     * @return type
     */
     
     public function editBackendLead(Request $request){
         try {
                $user_id = $request->get('user_id');
                $arr = [];    
                if($user_id){
                        $userInfo = $this->userRepo->getUserDetail($user_id);
                        $arr['full_name'] = $userInfo->f_name;
                        
                    }
                     
                    return view('backend.edit_lead');
                
         } catch (Exception $ex) {
             dd($ex);
         }
       
     }
     
     
}
