<?php
 
namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use Session;
use Auth;
use Mockery\Undefined;

class GstController extends Controller {

    public function __construct(InvMasterRepoInterface $master){
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->masterRepo = $master;
    }


    public function index(){
        return view('master.gst.index');
    }

    public function addGst(){
        return view('master.gst.add_gstTax');
    }

    public function saveGst(Request $request) {
        try {
            $arrSaveData = $request->all();
            $status = false;
            $tax_id = false;
            if(!empty($request->get('id'))){
                $tax_id = preg_replace('#[^0-9]#', '', $request->get('id'));
                $tax_data = $this->masterRepo->findGstById($tax_id);
                if(!empty($tax_data)) {
                    $status = $this->masterRepo->updateGST($arrSaveData, $tax_id);
                }
            }else{
                $arrSaveData['created_at'] = \carbon\Carbon::now();
                $status = $this->masterRepo->saveGst($arrSaveData); 
            }
            if($status){
                Session::flash('message', $tax_id ? trans('master_messages.gst_edit_success') :trans('master_messages.gst_add_success'));
                return redirect()->route('get_gst_list');
            }else{
                Session::flash('error', trans('master_messages.something_went_wrong'));
                return redirect()->route('get_gst_list');
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function editGst(Request $request){
        $gst_id = preg_replace('#[^0-9]#', '', $request->get('tax_id'));
        $gst_data = $this->masterRepo->findGstById($gst_id);
    	return view('master.gst.edit_gst',['gst_data' => $gst_data]);
    }
}