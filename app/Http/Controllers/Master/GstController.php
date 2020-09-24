<?php
 
namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use Session;
use Auth;
use Mockery\Undefined;
use Carbon\Carbon;

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
            $arrSaveData['igst'] =  $arrSaveData['tax_value'];
            $arrSaveData['cgst'] = $arrSaveData['tax_value'] / 2;
            $arrSaveData['sgst'] = $arrSaveData['tax_value'] / 2;

            if(!empty($request->get('id'))){
                $tax_id = preg_replace('#[^0-9]#', '', $request->get('id'));
                $tax_data = $this->masterRepo->findGstById($tax_id);
                if(!empty($tax_data)) {
                    $arrSaveData['tax_from'] = ($request['tax_from']) ? Carbon::createFromFormat('d/m/Y', $request['tax_from'])->format('Y-m-d') : '';
                    $status = $this->masterRepo->updateGST($arrSaveData, $tax_id);
                }
            }else{
                $e_date = Carbon::createFromFormat('d/m/Y', $request['tax_from'])->addDays(-1)->format('Y-m-d');
                $arrSaveData['tax_from'] = ($request['tax_from']) ? Carbon::createFromFormat('d/m/Y', $request['tax_from'])->format('Y-m-d') : '';
                $arrSaveData['created_at'] = \carbon\Carbon::now();
                $status = $this->masterRepo->saveGst($arrSaveData); 
                $this->masterRepo->updateGstEndDate($status->tax_id, $e_date);
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
        $gst_data['tax_from'] = $this->getFormatedDate($gst_data->tax_from);
        $gst_data['tax_to'] = $this->getFormatedDate(($gst_data->tax_to != null) ? $gst_data->tax_to : '');
    	return view('master.gst.edit_gst',['gst_data' => $gst_data]);
    }
    
    public function getFormatedDate($strDate) {
        if(!empty($strDate)){
            $arr = explode(" ", $strDate);
            $formated_date = explode("/", str_replace('-', '/', $arr[0]));
            $new_format = $formated_date[2] . '/' . $formated_date[1] . '/' . $formated_date[0];
            return $new_format;
        }
    }
}