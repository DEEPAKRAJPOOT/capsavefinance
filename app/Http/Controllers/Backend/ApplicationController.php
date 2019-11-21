<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\BusinessInformationRequest;
use App\Http\Requests\PartnerFormRequest;
use App\Http\Requests\DocumentRequest;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use App\Inv\Repositories\Models\Master\State;
use Session;
use Helpers;

class ApplicationController extends Controller
{
    protected $appRepo;
    protected $userRepo;
    protected $docRepo;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo){
        $this->appRepo = $app_repo;
        $this->userRepo = $user_repo;
        $this->docRepo = $doc_repo;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//       if($currStage){
//        Helpers::updateWfStage($currStage->stage_code, 1, $wf_status = 1);
//      }
       return view('backend.app.index');   
              
    }

    /**
     * Render view for company detail page according to biz id
     * 
     * @param Request $request
     * @return view
     */
    public function showCompanyDetails(Request $request){
        try {
            $arrFileData = $request->all();
            $business_info = $this->appRepo->getApplicationById($request->biz_id);
            $states = State::getStateList()->get();
            //dd($business_info->gst->pan_gst_hash);

            if ($business_info) {
                return view('backend.app.company_details')->with(['business_info'=>$business_info, 'states'=>$states]);
            } else {
                return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Update company detail page according to biz id
     * 
     * @param Request $request
     * @return to promoter detail page
     */
    public function updateCompanyDetail(BusinessInformationRequest $request){
        try {
            $arrFileData = $request->all();
            $appId = $request->app_id;
            $bizId = $request->biz_id;
            
            $business_info = $this->appRepo->updateCompanyDetail($arrFileData, $bizId, Auth::user()->user_id);

            if ($business_info) {
                Session::flash('message',trans('success_messages.update_company_detail_successfully'));
                return redirect()->route('promoter_details',1);
            } else {
                return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }


     /* Show promoter details page  */
     public function showPromoterDetails($bizId){
        $id = Auth::user()->user_id;
        $attribute['biz_id'] = $bizId;
        $OwnerPanApi = $this->userRepo->getOwnerApiDetail($attribute);
        return view('backend.app.promoter-details')->with('ownerDetails',$OwnerPanApi);
    }
    
    /**
     * Render view for change application status
     * 
     * @param Request $request
     * @return view
     */
    public function changeAppStatus(Request $request) {
        $app_id = $request->get('app_id');
        $biz_id = $request->get('biz_id');
        $appStatus = [''=>'Select status', '1'=>'Completed','0'=> 'Pending', '2' => 'Onhold'];
        
        return view('backend.app.change_app_status')
                ->with('app_id', $app_id)
                ->with('biz_id', $biz_id)
                ->with('appStatus', $appStatus);
    }
 
    /**
     * Render view for assign case
     * 
     * @param Request $request
     * @return view
     */    
    public function assignCase(Request $request) {
        $app_id = $request->get('app_id');
        $biz_id = $request->get('biz_id');
        $assignee = [
            '' => 'Select assignee',
            '1' => 'credit manager 1',
            '2' => 'credit manager 2',
            '3' => 'credit manager 3',
        ];
        return view('backend.app.assign_case')
                  ->with('app_id', $app_id)
                  ->with('biz_id', $biz_id)
                  ->with('assignee', $assignee);
    }  
    
    /**
     * Update application status
     * 
     * @param Request $request
     * @return view
     */    
    public function updateAppStatus(Request $request) {
        try {
            $app_id = $request->get('app_id');
            $biz_id = $request->get('biz_id');
            $app_status = $request->get('app_status');
            $updateData = ['app_status' => $app_status];
            
            $this->appRepo->updateAppStatus($app_id, $updateData);
            Session::flash('message',trans('backend_messages.change_app_status'));
            //return redirect()->route('company_details', ['app_id' => $app_id, 'biz_id' => $biz_id]);
            return redirect()->route('application_list');
        } catch (Exception $ex ) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
    /**
     * Save assign case
     * 
     * @param Request $request
     * @return view
     */    
    public function updateAssignee(Request $request) {
        try {
            $app_id = $request->get('app_id');
            $biz_id = $request->get('biz_id');
            $app_status = $request->get('assignee');
            $updateData = ['app_status' => $app_status];
            
            $this->appRepo->updateAssignee($app_id, $updateData);
            Session::flash('message',trans('backend_messages.update_assignee'));
            //return redirect()->route('company_details', ['app_id' => $app_id, 'biz_id' => $biz_id]);
            return redirect()->route('application_list');
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
    /**
     * Render view for add application note
     * 
     * @param Request $request
     * @return view
     */
    public function addAppNote(Request $request) {
        $app_id = $request->get('app_id');
        $biz_id = $request->get('biz_id');        
        
        return view('backend.app.add_app_note')
                ->with('app_id', $app_id)
                ->with('biz_id', $biz_id);
    } 
    
    /**
     * Save application note
     * 
     * @param Request $request
     * @return view
     */    
    public function saveAppNote(Request $request) {
        
        try {
            $app_id = $request->get('app_id');
            $biz_id = $request->get('biz_id');
            $notes = $request->get('notes');
            $noteData = [
                'app_id' => $app_id, 
                'note_data' => $notes,
                'created_at' => \Carbon\Carbon::now(),
                'created_by' => \Auth::user()->user_id
            ];
            
            $this->appRepo->saveAppNote($noteData);
            Session::flash('message',trans('backend_messages.add_note'));
            //return redirect()->route('company_details', ['app_id' => $app_id, 'biz_id' => $biz_id]);
            return redirect()->route('application_list');
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    } 
    
    /**
     * Save application note
     * 
     * @param Request $request
     * @return view
     */    
    public function sendCaseConfirmbox(Request $request) {
        try{
            $user_id = $request->get('user_id');
            $app_id = $request->get('app_id');
           return view('backend.app.next_stage_confirmBox')
                ->with('app_id', $app_id)
                ->with('user_id', $user_id);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    /**
     * Save application note
     * 
     * @param Request $request
     * @return view
     */    
    public function AcceptNextStage(Request $request) {
        try{
            
            $user_id = $request->get('user_id');
            $app_id = $request->get('app_id');
            
            $currStage = Helpers::getCurrentWfStage($app_id);
            Helpers::updateWfStage($currStage->stage_code, 1, $wf_status = 1);
            //$this->appRepo->updateAppDetails($app_id,['is_assigned'=>0]);
            
           $application = $this->appRepo->updateAppDetails($app_id, ['is_assigned'=>1]); 
           Session::flash('is_accept', 1);
            return redirect()->back();
           
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
    
}