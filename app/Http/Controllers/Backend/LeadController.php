<?php

namespace App\Http\Controllers\Backend;
use Auth;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;

class LeadController extends Controller
{
    
     protected $userRepo;
 
      protected $appRepo;
      
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    
    public function __construct( InvUserRepoInterface $user,InvAppRepoInterface $app_repo)
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('checkBackendLeadAccess');

        $this->userRepo = $user;
        $this->appRepo = $app_repo;
         
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
     
      
    /**
     *backend Lead Details
     * 
     * @param Request $request
     * @return type
     */
     
     public function leadDetail(Request $request){
         try {
                $user_id = $request->get('user_id');
                $userInfo = $this->userRepo->getUserDetail($user_id);//dd($userInfo);
                $application = $this->appRepo->getApplicationsDetail($user_id)->toArray();
                 return view('backend.lead.lead_details')
                            ->with('userInfo' ,$userInfo)
                            ->with('application' ,$application);
                
                
         } catch (Exception $ex) {
             dd($ex);
         }
       
     }
          
    /**
     *backend Lead Details
     * 
     * @param Request $request
     * @return type
     */
     
     public function showApplicationPool(){
         try {
                return view('backend.app.case_poll');
                
         } catch (Exception $ex) {
             dd($ex);
         }
       
     }
     
    /**
     *backend Lead Details
     * 
     * @param Request $request
     * @return type
     */
     
     public function confirmBox(Request $request){
         try {
             //dd($request->all());
             $user_id = $request->get('user_id');
             $app_id = $request->get('app_id');
               
             return view('backend.app.confirmBox')
             ->with('user_id', $user_id)
                     ->with('app_id', $app_id);
                
         } catch (Exception $ex) {
             dd($ex);
         }
       
     }
     
     
    /**
     *backend Lead Details
     * 
     * @param Request $request
     * @return type
     */
     
     public function acceptApplicationPool(Request $request){
         try {
             
             $user_id = $request->get('user_id');
             $app_id = $request->get('app_id');
            
             $dataArr = []; 
             $dataArr['from_id'] = Auth::user()->user_id;
             $dataArr['to_id'] = Auth::user()->user_id;
             $dataArr['assigned_user_id'] = $user_id;
             $dataArr['app_id'] = $app_id;
             $dataArr['assign_status'] = '0';
             $dataArr['sharing_comment'] = "comment";
             $dataArr['is_owner'] = 1;
            $application = $this->appRepo->updateAppDetails($app_id, ['is_assignd'=>1]); 
            $application = $this->appRepo->saveShaircase($dataArr); 
             
             Session::flash('is_accept', 1);
           
              return redirect()->back();
            
            
                
         } catch (Exception $ex) {
             dd($ex);
         }
       
     }
     
     
     
     
     
}
