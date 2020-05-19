<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    
     
    
    public function __construct() {
        $this->middleware('guest')->except('logout');
        $this->middleware('checkBackendLeadAccess');
    }

    public function index(Request $request) {
        try {
            return view('reports.summary');
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
        
    }

    public function customer(Request $request) {
        try {
            return view('reports.customer');
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
        
    }
    public function bank(Request $request) {
        try {
            return view('reports.bank');
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
        
    }
    public function company(Request $request) {
        try {
            return view('reports.company');
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
        
    }
    
     public function duereport(Request $request) {
         
        try {
            return view('reports.duereport');
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
        
    }
    
     public function overduereport(Request $request) {
        try {
            return view('reports.overduereport');
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
        
    }
}