<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\PdNotesRequest;
use Illuminate\Http\Request;
use Auth;
use Session;
use App\Inv\Repositories\Contracts\QmsInterface as InvQmsRepoInterface;

class QmsController extends Controller {

    protected $qmsRepo;

    public function __construct(InvQmsRepoInterface $qms_repo)
    {
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->qmsRepo = $qms_repo;
    }

    public function index(Request $request)
    {
        $app_id = $request->get('app_id');
        $arrData = [];
      //  $arrData = $this->appRepo->showData($app_id);
        return view('backend.qms.queryList', compact('arrData', 'app_id'));
    }


    public function showQueryForm(Request $request)
    {
        $app_id = $request->get('app_id');
        $arrData = [];
        return view('backend.qms.queryForm', compact('arrData', 'app_id'));
    }
    
}
