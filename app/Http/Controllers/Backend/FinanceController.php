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

    public function getFinAccount() {
        return view('backend.finance.account_list');
    }    

    public function getFinVariable() {
        return view('backend.finance.variable_list');
    }  
    public function crateJeConfig() {
        return view('backend.finance.je_config');
    }  
}
