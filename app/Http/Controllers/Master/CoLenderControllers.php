<?php

namespace App\Http\Controllers\Master;

use Auth;
use Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;

class CoLenderControllers extends Controller {

    public function __construct(InvMasterRepoInterface $master)
    {
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->masterRepo = $master;
    }

    /**
     * co lenders list
     * 
     * @return mixed
     */
    public function getColenders()
    {
        return view('backend.coLenders.co_lenders_list');
    }

    /**
     * add co lender
     * 
     * @return mixed
     */
    public function addCoLender()
    {
        return view('backend.coLenders.add_co_lender_frm');
    }

    public function saveCoLender()
    {
        
    }

}
