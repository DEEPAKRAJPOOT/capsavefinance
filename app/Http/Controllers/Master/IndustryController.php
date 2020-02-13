<?php
 
namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use Session;
use Auth;
 
class IndustryController extends Controller {

    public function __construct(InvMasterRepoInterface $master){
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->masterRepo = $master;
    }


     public function index(){
        return view('master.industries.index');
    }

    public function addIndustry(){
    	return view('master.industries.add_industries');
    }

    public function editIndustry(Request $request){
        $industry_id = preg_replace('#[^0-9]#', '', $request->get('id'));
        $industries_data = $this->masterRepo->findIndustryById($industry_id);
    	return view('master.industries.edit_industries',['industries_data' => $industries_data]);
    }


    public function saveIndustries(Request $request) {
        try {
            $arrIndustriesData = $request->all();
            $status = false;
            $industry_id = false;
            if(!empty($request->get('id'))){
                $industry_id = preg_replace('#[^0-9]#', '', $request->get('id'));
                $industries_data = $this->masterRepo->findIndustryById($industry_id);
                if (!empty($industries_data)) {
                    $arrIndustriesData['updated_by'] = Auth::user()->user_id;
                    $status = $this->masterRepo->updateIndustries($arrIndustriesData, $industry_id);
                }
            }else{
               $arrIndustriesData['created_by'] = Auth::user()->user_id;
               $status = $this->masterRepo->saveIndustries($arrIndustriesData); 
            }
            if($status){
                Session::flash('message', $industry_id ? trans('master_messages.industry_edit_success') :trans('master_messages.industry_add_success'));
                return redirect()->route('get_industries_list');
            }else{
                Session::flash('error', trans('master_messages.something_went_wrong'));
                return redirect()->route('get_industries_list');
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
}