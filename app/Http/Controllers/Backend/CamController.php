<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CamController extends Controller
{
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
    }

    public function finance()
    {
        return view('backend.cam.finance');

    }
    public function finance_store(Request $request)
    {
        dd($request);

    }

}
