<?php

namespace App\Http\Controllers\Backend;

use Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class FinanceController extends Controller {


    public function __construct() {
        $this->middleware('guest')->except('logout');
        $this->middleware('checkBackendLeadAccess');
    }


    public function getFinTransList() {
        return view('backend.finance.trans_list');
    }    

    public function getFinJournal() {
        return view('backend.finance.journal_list');
    }  

}
