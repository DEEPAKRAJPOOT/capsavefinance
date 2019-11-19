<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Models\Cam;
use Auth;
use Session;

class CamController extends Controller
{
     protected $appRepo;
	  public function __construct(){
        $this->middleware('auth');
       
        }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.cam.overview');
    }

    public function camInformationSave(Request $request)
    {
    	$arrCamData = $request->all();
        $arrCamData['biz_id'] = '12';
        $arrCamData['app_id'] = '12';
        $userId = Auth::user()->user_id;
        if(!isset($arrCamData['rating_no'])){
            $arrCamData['rating_no'] = NULL;
        }
        Cam::creates($arrCamData, $userId);
        Session::flash('message',trans('Cam Information Saved Successfully'));
        return redirect()->route('cam_overview');
    }

    public function finance()
    {
        return view('backend.cam.finance');

    }

}
