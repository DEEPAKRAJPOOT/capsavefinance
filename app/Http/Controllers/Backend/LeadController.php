<?php

namespace App\Http\Controllers\Backend;
use Auth;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use Event;
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
            $application = $this->appRepo->updateAppDetails($app_id, ['is_assigned'=>1]); 
            $application = $this->appRepo->saveShaircase($dataArr); 
             
             Session::flash('is_accept', 1);
           
              return redirect()->back();
            
            
                
         } catch (Exception $ex) {
             dd($ex);
         }
       
     }
     
     
     
     
  
 /**
     * Display anchor listing
     *
     * @return \Illuminate\Http\Response
     */
    public function allAnchorList()
    {
        return view('backend.anchor.index');
    }
    
    
     public function addAnchorReg(Request $request){
         try {               
             return view('backend.anchor.add_anchor_reg');
                
         } catch (Exception $ex) {
             dd($ex);
         }
       
     }
     
     
      public function saveaddAnchorReg(Request $request) {
       try {
            $arrAnchorData = $request->all();
            $arrAnchorData = [
                'comp_name' => $arrAnchorData['comp_name'],  
                'comp_email' => $arrAnchorData['email'],
                'comp_phone' => $arrAnchorData['phone'],
                'comp_state' => $arrAnchorData['state'],
                'comp_city' => $arrAnchorData['city'],
                'comp_zip' => $arrAnchorData['pin_code']
            ];
             $arrAnchUserData = [
                    'user_id' => 0,
                    'name' =>  $arrAnchorData['comp_name'],  
                    'email' => $arrAnchorData['comp_email'],
                    'phone' => $arrAnchorData['comp_phone'],
                    'is_registered' => '0'
            ];
            $anchor_info = $this->userRepo->saveAnchor($arrAnchorData);
            $anchor_user_info = $this->userRepo->saveAnchorUser($arrAnchUserData);
            //Auth::user()->id
          if ($anchor_info && $anchor_user_info) {
              //return redirect()->route('get_anchor_list')->with('message', trans('success_messages.basic_saved_successfully'));
              //Session::flash('message',trans('success_messages.basic_saved_successfully'));
                //return redirect()->route('manage-anchor');
              $mailUrl=config('proin.frontend_uri').'/sign-up?email='.$arrAnchUserData['email'];
              $anchUserMailArr = [];
                $anchUserMailArr['email'] = $arrAnchUserData['email'];
                $anchUserMailArr['name'] = $arrAnchUserData['name'];
                $anchUserMailArr['url'] = $mailUrl;
                Event::dispatch("ANCHOR_REGISTER_USER_MAIL", serialize($anchUserMailArr));
              
                 Session::flash('message',trans('backend_messages.change_app_status'));
                  return redirect()->route('get_anchor_list');


                //return response()->json(['message' =>trans('success_messages.basic_saved_successfully'),'status' => 1]);
            } else {
              // return response()->json(['message' =>trans('success_messages.oops_something_went_wrong'),'status' => 0]);
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
}
