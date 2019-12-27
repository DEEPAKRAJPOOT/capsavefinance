<?php
 
namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use Session;
use Auth;
 
class EntityController extends Controller {

    public function __construct(InvMasterRepoInterface $master){
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->masterRepo = $master;
    }


     public function index(){
        return view('master.entities.index');
    }

    public function addEntity(){
           return view('master.entities.add_entities');
        
    }

    public function saveEntity(Request $request) {
        try {
            $arrEntityData = $request->all();
            $arrEntityData['created_at'] = \carbon\Carbon::now();
            $arrEntityData['created_by'] = Auth::user()->user_id;
            $status = false;
            $entity_id = false;
            if(!empty($request->get('id'))){
                $entity_id = preg_replace('#[^0-9]#', '', $request->get('id'));
                $entity_data = $this->masterRepo->findEntityById($entity_id);
                if(!empty($entity_data)) {
                    $status = $this->masterRepo->updateEntity($arrEntityData, $entity_id);
                }
            }else{
               $status = $this->masterRepo->saveEntity($arrEntityData); 
            }
            if($status){
                Session::flash('message', $entity_id ? trans('master_messages.entity_edit_success') :trans('master_messages.entity_add_success'));
                return redirect()->route('get_entity_list');
            }else{
                Session::flash('error', trans('master_messages.something_went_wrong'));
                return redirect()->route('get_entity_list');
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function editEntity(Request $request){
        $entity_id = preg_replace('#[^0-9]#', '', $request->get('id'));
        $entity_data = $this->masterRepo->findEntityById($entity_id);
    	return view('master.entities.edit_entity',['entity_data' => $entity_data]);
    }

 
}