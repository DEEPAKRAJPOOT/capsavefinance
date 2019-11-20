<?php

namespace App\Http\Controllers\Backend;
use Auth;
use Session;
use Crypt;
use Helpers;
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
             $anchorUserinfo=$request->get('anchor_id');
             if($anchorUserinfo){
                 $anchorVal=$this->userRepo->getAnchorById($anchorUserinfo);
                 
             }
             return view('backend.anchor.add_anchor_reg');
                
         } catch (Exception $ex) {
             dd($ex);
         }
       
     }
     
      public function saveaddAnchorReg(Request $request) {
       try {
           //$string = Helpers::randomPassword();
           $string ='Admin@123';
            $arrAnchorVal = $request->all();
            $arrAnchorData = [
                'comp_name' => $arrAnchorVal['comp_name'],  
                'comp_email' => $arrAnchorVal['email'],
                'comp_phone' => $arrAnchorVal['phone'],
                'comp_state' => $arrAnchorVal['state'],
                'comp_city' => $arrAnchorVal['city'],
                'comp_zip' => $arrAnchorVal['pin_code']
            ];
            $anchor_info = $this->userRepo->saveAnchor($arrAnchorData);

                $arrAnchUserData = [
                'anchor_id' => $anchor_info,
                'f_name' =>  $arrAnchorVal['employee'],
                'biz_name' =>  $arrAnchorData['comp_name'],
                'email' => $arrAnchorData['comp_email'],
                'mobile_no' => $arrAnchorData['comp_phone'],
                'user_type' => 2,
                'is_email_verified'=>1,
                'is_active'=>1,
                'password' => bcrypt($string)
                ];
                //dd($arrAnchUserData);
            $anchor_user_info = $this->userRepo->save($arrAnchUserData);
                 $anchUserMailArr = [];
                $anchUserMailArr['email'] = $arrAnchUserData['email'];
                $anchUserMailArr['name'] = $arrAnchUserData['f_name'];
                 $anchUserMailArr['password'] =$string;
                Event::dispatch("ANCHOR_REGISTER_USER_MAIL", serialize($anchUserMailArr));
          if ($anchor_info && $anchor_user_info) {
             
              
                 Session::flash('message',trans('backend_messages.anchor_registration_success'));
                  return redirect()->route('get_anchor_list');
                  
          } else {
              // return response()->json(['message' =>trans('success_messages.oops_something_went_wrong'),'status' => 0]);
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
    public function uploadAnchorlead(Request $request){
         try {               
             return view('backend.anchor.upload_anchor_lead');
                
         } catch (Exception $ex) {
             dd($ex);
         }
       
     }
     public function saveUploadAnchorlead(Request $request){
         try {
            $uploadedFile = $request->file('anchor_lead');
            $destinationPath = storage_path() . '/uploads';
            $fileName = time() . '.csv';
            if ($uploadedFile->isValid()) {
                $uploadedFile->move($destinationPath, $fileName);
            }
            $fileD = fopen($destinationPath . '/' . $fileName, "r");
            $column = fgetcsv($fileD);
            while (!feof($fileD)) {
                $rowData[] = fgetcsv($fileD);
            }

             $anchLeadMailArr = [];
             $arrAnchLeadData =[];
            foreach ($rowData as $key => $value) {
                $hashval = time() . 'ANCHORLEAD' . $key;
                $token = md5($hashval);
                $arrAnchLeadData = [
                    'name' => $value[0],
                    'email' => $value[1],
                    'phone' => $value[2],
                    'created_at' => \Carbon\Carbon::now(),
                    'token' => $token,
                ];
                $anchor_lead = $this->userRepo->saveAnchorUser($arrAnchLeadData);
                if ($anchor_lead) {
                    $mailUrl = config('proin.frontend_uri') . '/sign-up?token=' . $token;
                    $anchLeadMailArr['name'] = $arrAnchLeadData['name'];
                    $anchLeadMailArr['email'] = $arrAnchLeadData['email'];
                    $anchLeadMailArr['url'] = $mailUrl;
                    Event::dispatch("ANCHOR_CSV_LEAD_UPLOAD", serialize($anchLeadMailArr));
                }
            }
            unlink($destinationPath . '/' . $fileName);
            Session::flash('message', trans('backend_messages.anchor_registration_success'));
            return redirect()->route('lead_list');
        } catch (Exception $ex) {
            dd($ex);
        }
    }
     
     
     
}
