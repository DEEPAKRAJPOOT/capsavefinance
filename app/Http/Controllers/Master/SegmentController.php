<?php
 
namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use Session;
use Auth;
 
class SegmentController extends Controller {

    public function __construct(InvMasterRepoInterface $master){
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->masterRepo = $master;
    }


     public function index(){
        return view('master.segments.index');
    }

    public function addSegment(){
           return view('master.segments.add_segment');
        
    }

    public function saveSegment(Request $request) {
        try {
            $arrSegmentData = $request->all();
            $status = false;
            $segment_id = false;
            if(!empty($request->get('id'))){
                $segment_id = preg_replace('#[^0-9]#', '', $request->get('id'));
                $consti_data = $this->masterRepo->findSegmentById($segment_id);
                if(!empty($consti_data)) {
                    $status = $this->masterRepo->updateSegment($arrSegmentData, $segment_id);
                }
            }else{
                $arrSegmentData['created_at'] = \carbon\Carbon::now();
                $status = $this->masterRepo->saveSegment($arrSegmentData); 
            }
            if($status){
                Session::flash('message', $segment_id ? trans('master_messages.segment_edit_success') :trans('master_messages.segment_add_success'));
                return redirect()->route('get_segment_list');
            }else{
                Session::flash('error', trans('master_messages.something_went_wrong'));
                return redirect()->route('get_segment_list');
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function editSegment(Request $request){
        $segment_id = preg_replace('#[^0-9]#', '', $request->get('id'));
        $segment_data = $this->masterRepo->findSegmentById($segment_id);
    	return view('master.segments.edit_segment',['segment_data' => $segment_data]);
    }

 
}