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
use Session;

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
        return view('backend.app.index');
    }

    public function showCompanyDetails(Request $request){
        try {
            $arrFileData = $request->all();
            $business_info = $this->appRepo->getApplicationById($request->app_id);
            //dd($business_info);

            if ($business_info) {
                Session::flash('message',trans('success_messages.basic_saved_successfully'));
                return view('backend.app.company-details')->with(['business_info'=>$business_info]);
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
        return view('backend.app.promoter-details')->;
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
}