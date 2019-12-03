<?php

namespace App\Http\Controllers\Backend;

use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Helpers;
use Auth;

class FiRcuController extends Controller
{
    protected $appRepo;
    protected $userRepo;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo){
        $this->appRepo = $app_repo;
        $this->userRepo = $user_repo;
    }
    
    /**
     * Display a listing of FI
     */
    public function listFI(Request $request)
    {
        $biz_id = $request->get('biz_id');
        $fiLists = $this->appRepo->getAddressforFI($biz_id);
        //dd($fiLists);
        return view('backend.fircu.fi')->with('fiLists', $fiLists);   
    }

    /**
     * Display a listing of RCU
     */
    public function listRCU(Request $request)
    {
        $appId = $request->get('app_id');
        $rcuResult = $this->appRepo->getRcuLists($appId);
//        $temp = [];
//        foreach ($rcuResult as $key => $value) { 
//            if(in_array($value->doc_id, $temp)){
//                $temp[] = $value->doc_id;
//                continue;
//            } else {
//                $temp[] = $value->doc_id;
//            }
//        }
//        dd($rcuResult);
        return view('backend.fircu.rcu', [
                    'data' => $rcuResult
                ]);   
    }


}
