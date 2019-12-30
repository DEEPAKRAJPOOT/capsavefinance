<?php
 
namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use Session;
use Auth;
 
class DoaController extends Controller {

    public function __construct(InvMasterRepoInterface $master)
    {
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->masterRepo = $master;
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
        foreach($states as $state) {
            $stateList[$state->id] = $state->name;
        }
            
        if ($request->has('doa_level_id')) {
            $doa_level_id = preg_replace('#[^0-9]#', '', $request->get('doa_level_id'));
            $doa_level = $this->masterRepo->getDoaLevelById($doa_level_id);
            $level_code = $doa_level->level_code;
        } else {
            $doa_level = new \stdClass();
            $level_code = $this->getDoaLevelCode();
        }
        
        $data = ['doaLevel' => $doa_level, 'stateList' => $stateList, 'levelCode' => $level_code];
    	return view('master.doa.add_doa_level', $data);
    }
    
    /**
     * Generate DoA Level Code
     * 
     */
    protected function getDoaLevelCode()
    {
        $doa_level = $this->masterRepo->getLatestDoaData();
        $doa_level_id = $doa_level ? $doa_level->doa_level_id : 1;
        $level_code = "LEVEL_" . $doa_level_id;
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
        try {
            $level_code = $this->getDoaLevelCode();
            $reqData = $request->all();
            $data = [
                'level_code' => $level_code,
                'level_name' => $reqData['level_name'],
                'state_id' => $reqData['state_id'],
                'city_id' => $reqData['city_id'],
                'min_amount' => $reqData['min_amount'],
                'max_amount' => $reqData['max_amount'],
            ];
            $doa_level_id = $reqData['doa_level_id'] ? $reqData['doa_level_id'] : null;
            $this->masterRepo->saveDoaLevelData($data, $doa_level_id);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
}
