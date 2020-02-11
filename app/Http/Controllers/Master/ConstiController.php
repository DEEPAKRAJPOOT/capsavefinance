<?php
 
namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use Session;
use Auth;
 
class ConstiController extends Controller {

    public function __construct(InvMasterRepoInterface $master){
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->masterRepo = $master;
    }


     public function index(){
        return view('master.constitution.index');
    }

    public function addConstitution(){
           return view('master.constitution.add_constitution');
        
    }

    public function saveConstitution(Request $request) {
        try {
            $arrConstiData = $request->all();
            $status = false;
            $consti_id = false;
            if(!empty($request->get('id'))){
                $consti_id = preg_replace('#[^0-9]#', '', $request->get('id'));
                $consti_data = $this->masterRepo->findConstitutionById($consti_id);
                if(!empty($consti_data)) {
                    $status = $this->masterRepo->updateConstitution($arrConstiData, $consti_id);
                }
            }else{
                $arrConstiData['created_at'] = \carbon\Carbon::now();
                $status = $this->masterRepo->saveConstitution($arrConstiData); 
            }
            if($status){
                Session::flash('message', $consti_id ? trans('master_messages.consti_edit_success') :trans('master_messages.consti_add_success'));
                return redirect()->route('get_constitutions_list');
            }else{
                Session::flash('error', trans('master_messages.something_went_wrong'));
                return redirect()->route('get_constitutions_list');
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function editConstitution(Request $request){
        $consti_id = preg_replace('#[^0-9]#', '', $request->get('id'));
        $consti_data = $this->masterRepo->findConstitutionById($consti_id);
    	return view('master.constitution.edit_constitution',['consti_data' => $consti_data]);
    }

 
}