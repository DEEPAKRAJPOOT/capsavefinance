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
    
}