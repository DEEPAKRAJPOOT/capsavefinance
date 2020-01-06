<?php
 
namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use Session;
use Auth;
 
class DoaController extends Controller {

    public function __construct(InvMasterRepoInterface $master, InvUserRepoInterface $userRepo)
    {
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->masterRepo = $master;
        $this->userRepo = $userRepo;
    }

    /**
     * List DoA Levels
     * 
     * @return response
     */
    public function index()
    {
        return view('master.doa.index');
    }

    /**
     * Add DoA Level
     * 
     * @param Request $request
     * @return response
     */
    public function addDoaLevel(Request $request)
    {
        $states = $this->masterRepo->getState();
        $stateList = [];
        $cityList = [];
        foreach($states as $state) {
            $stateList[$state->id] = $state->name;
        }
            
        if ($request->has('doa_level_id')) {
            $doa_level_id = preg_replace('#[^0-9]#', '', $request->get('doa_level_id'));
            $doa_level = $this->masterRepo->getDoaLevelById($doa_level_id);
            $level_code = $doa_level->level_code;
            
            $cities = $this->masterRepo->getcity($doa_level->state_id);
            foreach($cities as $city) {
                $cityList[$city->id] = $city->name;
            }
            
        } else {
            $doa_level = new \stdClass();
            $level_code = $this->getDoaLevelCode();
        }
        
        $data = [
            'doaLevel'  => $doa_level, 
            'stateList' => $stateList, 
            'levelCode' => $level_code,
            'cityList'  => $cityList
        ];
    	return view('master.doa.add_doa_level', $data);
    }
    
    /**
     * Generate DoA Level Code
     * 
     */
    protected function getDoaLevelCode($doa_level_id=null)
    {
        if (is_null($doa_level_id)) {
            $doa_level = $this->masterRepo->getLatestDoaData();
            $doa_level_id = $doa_level ? $doa_level->doa_level_id : 0;
            $code_index = $doa_level_id + 1;
        } else {
             $code_index = $doa_level_id;
        }
        $level_code = "LEVEL_" . $code_index;
        
        return $level_code;
    }

    /**
     * Save DoA Level
     * 
     * @param Request $request
     * @return response
     */
    public function saveDoaLevel(Request $request) 
    {
        $reqData = $request->all();
        try {
            $data = [
                'level_code' => $reqData['level_code'],
                'level_name' => $reqData['level_name'],
                'state_id'   => $reqData['state_id'],
                'city_id'    => $reqData['city_id'],
                'min_amount' => $reqData['min_amount'],
                'max_amount' => $reqData['max_amount'],
            ];
            
            $doa_level_id = $reqData['doa_level_id'] ? $reqData['doa_level_id'] : null;
            if (is_null($doa_level_id)) {
                $whereCond = [
                    'city_id'    => $reqData['city_id'],
                    'min_amount' => $reqData['min_amount'],
                    'max_amount' => $reqData['max_amount']
                ];
                $doaData = $this->masterRepo->getDoaLevelData($whereCond);                
                //$doa_level_id = $doaData ? $doaData->doa_level_id : null;
                //$data['level_code'] = $this->getDoaLevelCode($doa_level_id);
                if ($doaData) {
                    Session::flash('is_data_found', '1');                    
                    return redirect()->back();                    
                }
            }
            
            $this->masterRepo->saveDoaLevelData($data, $doa_level_id);                        

            //Session::flash('message', 'Doa Level saved successfully!');
            //return redirect()->route('manage_doa');
            Session::flash('is_data_saved', '1');   
            return redirect()->back();
            
            
        } catch (Exception $ex) {
            Session::flash('is_data_saved', '1'); 
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
    /**
     * View assign role
     * 
     * @param Request $request
     * @return response
     */
    public function assignRoleLevel(Request $request)
    {
        $doa_level_id = preg_replace('#[^0-9]#', '', $request->get('doa_level_id'));
        $doa_level = $this->masterRepo->getDoaLevelById($doa_level_id);
        $level_code = $doa_level->level_code;

        $roles = $this->userRepo->getRolesByType(2);
        $roleList = [];
        foreach($roles as $role) {
            $roleList[$role->id] = $role->name;
        }
        
        $levelRoles = $this->masterRepo->getDoaLevelRoles($doa_level_id);
        $levelRoleList = [];
        foreach($levelRoles as $levelRole) {
            $levelRoleList[] = $levelRole->role_id;
        }    
        $data = [
            'levelName'  => $doa_level->level_name,
            'limitAmount'  => $doa_level->min_amount . ' - ' . $doa_level->max_amount,
            'city'  => $doa_level->city,
            'roleList'  => $roleList,
            'doaLevelRoles' => $levelRoleList,
            'doaLevelId' => $doa_level_id
        ];
    	return view('master.doa.assign_doa_role', $data);        
    }
    
    /**
     * Save assign role 
     * 
     * @param Request $request
     * @return response
     */
    public function saveAssignRoleLevel(Request $request)
    {
        $reqData = $request->all();
        try { 
            $doa_level_id = $reqData['doa_level_id'];
            $roles = $reqData['role'];            
            
            $this->masterRepo->deleteDoaLevelRoles($doa_level_id);
            foreach($roles as $role) {
                $data[] = [
                    'doa_level_id' => $doa_level_id,
                    'role_id' => $role,
                ];
            }
            $this->masterRepo->saveDoaLevelRoles($data);
            
            Session::flash('message', 'Roles are assigned successfully.');
            return redirect()->route('manage_doa');
                
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
}
