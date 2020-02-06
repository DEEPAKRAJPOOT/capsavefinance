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
        $doaLevelStates = [];    
        $doa_level_id = null;
        if ($request->has('doa_level_id')) {
            $doa_level_id = preg_replace('#[^0-9]#', '', $request->get('doa_level_id'));
            $doa_level = $this->masterRepo->getDoaLevelById($doa_level_id);
            
            $level_code = isset($doa_level->level_code) ? $doa_level->level_code : null ;
            $doaLevelStates = isset($doa_level->doaLevelStates) ? $doa_level->doaLevelStates->toArray() : [];
            if (isset($doa_level->state_id)) {
                $cities = $this->masterRepo->getcity($doa_level->state_id);
                foreach ($cities as $city) {
                    $cityList[$city->id] = $city->name;
                }
            }
        } else {
            $doa_level = new \stdClass();
            $level_code = $this->getDoaLevelCode();
        }
          $levelRoleList = [];
        
        if ($request->has('doa_level_id')) {
            $levelRoles = $this->masterRepo->getDoaLevelRoles($doa_level_id);


            foreach ($levelRoles as $levelRole) {
                if (in_array($levelRole->role_id, $levelRoleList)) {
                    array_push($levelRoleList[$levelRole->role_id], $levelRole->user_id);
                } else {
                    $levelRoleList[$levelRole->role_id][] = $levelRole->user_id;
                }
            }



            $levelRoles = $this->masterRepo->getDoaLevelRoles($doa_level_id);
            $levelRoleList = [];

            foreach ($levelRoles as $levelRole) {
                if (in_array($levelRole->role_id, $levelRoleList)) {
                    array_push($levelRoleList[$levelRole->role_id], $levelRole->user_id);
                } else {
                    $levelRoleList[$levelRole->role_id][] = $levelRole->user_id;
                }
            }
        }
        
        
        
        $roles = $this->userRepo->getRolesByType(2);
        $roleList = [];
        foreach($roles as $role) {
            $roleList[$role->id] = $role->name;
        }












        $data = [
            'doaLevel' => $doa_level,
            'stateList' => $stateList,
            'levelCode' => $level_code,
            'cityList' => $cityList,
            'doaLevelStates' => $doaLevelStates,
            'roleList' => $roleList,
            'doaLevelRoles' => $levelRoleList,
            'doaLevelId' => $doa_level_id
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
     * Save DOA level states
     * 
     * @param Array $request 
     * @param int $doa_id 
     */
    function saveDeoLevelStates($request, $doa_id)
    {
        try {
            $state_id = $request->get('state_id');
            $city_id = $request->get('city_id');
            $prepreDeoLevelData = [];
            foreach ($state_id as $keys => $values):
                $prepreDeoLevelData[$keys] = [
                    'doa_level_id' => $doa_id,
                    'state_id' => isset($values) ? $values : null,
                    'city_id' => isset($city_id[$keys]) ? $city_id[$keys] : null,
                ];
            endforeach;
          
            $this->masterRepo->deleteDeoLevelStates(['doa_level_id'=>$doa_id]);
            $this->masterRepo->saveDeoLevelStates($prepreDeoLevelData);
        } catch (Exception $ex) {
            throw $ex;
        }
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
                //'state_id'   => $reqData['state_id'],
                // 'city_id'    => $reqData['city_id'],
                'min_amount' => str_replace(',', '', $reqData['min_amount']),
                'max_amount' => str_replace(',', '', $reqData['max_amount'])
            ];

            $doa_level_id = $reqData['doa_level_id'] ? $reqData['doa_level_id'] : null;
            if (is_null($doa_level_id)) {
                $whereCond = [
                    'city_id' => $reqData['city_id'],
                    'min_amount' => str_replace(',', '', $reqData['min_amount']),
                    'max_amount' => str_replace(',', '', $reqData['max_amount'])
                ];



                $doaData = $this->masterRepo->getDoaLevelData($whereCond);
                //$doa_level_id = $doaData ? $doaData->doa_level_id : null;
                //$data['level_code'] = $this->getDoaLevelCode($doa_level_id);
                if ($doaData) {
                    Session::flash('is_data_found', '1');
                    return redirect()->back();
                }
            }

            $lastInsertId = $this->masterRepo->saveDoaLevelData($data, $doa_level_id);
            $lastInsertId = $doa_level_id ? $doa_level_id : $lastInsertId->doa_level_id;
            $this->saveDeoLevelStates($request, $lastInsertId);



            $role_user_id = $reqData['role_user'];
            $prepareData = [];
            $roles = $reqData['role'];
            foreach ($role_user_id as $keys => $values):
                $role_id = isset($roles[$keys]) ? $roles[$keys] : null;
                foreach ($values as $inkeys => $inValue) {
                    $prepareData[] = [
                        'doa_level_id' => $lastInsertId,
                        'role_id' => $role_id,
                        'user_id' => isset($inValue) ? $inValue : null
                    ];
                }
            endforeach;
            // dd($prepareData);
            $this->masterRepo->deleteDoaLevelRoles($doa_level_id);
            $this->masterRepo->saveDoaLevelRoles($prepareData);




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
        
        foreach ($levelRoles as $levelRole) {
            if (in_array($levelRole->role_id, $levelRoleList)) {
                array_push($levelRoleList[$levelRole->role_id], $levelRole->user_id);
            } else {
                $levelRoleList[$levelRole->role_id][] = $levelRole->user_id;
            }
        }
     //   dd($levelRoles->toArray());
        $getCityData = ($doa_level->doaLevelStates) ? $doa_level->doaLevelStates->toArray() : [];
       $selectedCity = array_reduce($getCityData, function ($out,$elem) {
           $out[] =  $elem['name'];
           return  $out;
       },[]);
      
       
        $data = [
            'levelName'  => $doa_level->level_name,
            'limitAmount'  => $doa_level->min_amount . ' - ' . $doa_level->max_amount,
            'city'  => implode(',', $selectedCity),
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
            $role_user_id = $reqData['role_user'];
            $prepareData = [];
            foreach ($role_user_id as $keys => $values):
                $role_id = isset($roles[$keys]) ? $roles[$keys] : null;
                foreach ($values as $inkeys => $inValue) {
                    $prepareData[] = [
                        'doa_level_id' => $doa_level_id,
                        'role_id' => $role_id,
                        'user_id' => isset($inValue) ? $inValue : null
                    ];
                }
            endforeach;
            $this->masterRepo->deleteDoaLevelRoles($doa_level_id);
            $this->masterRepo->saveDoaLevelRoles($prepareData);
            Session::flash('message', 'Roles are assigned successfully.');
            return redirect()->route('manage_doa');
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

}
