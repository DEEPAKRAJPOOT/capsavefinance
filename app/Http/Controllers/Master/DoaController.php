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

    public function index()
    {
        return view('master.doa.index');
    }

    public function addDoaLevel()
    {
    	return view('master.doa.add_doa_level');
    }

    public function editDoaLevel(Request $request)
    {
        $doa_level_id = preg_replace('#[^0-9]#', '', $request->get('doa_level_id'));
        $doa_level = $this->masterRepo->getDoaLevelById($doa_level_id);
    	return view('master.doa.edit_doa_level', ['doa_level' => $doa_level]);
    }

    public function saveDoaLevel(Request $request) 
    {
        try {
            
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
}
