<?php

namespace App\Http\Controllers\Backend;

use Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\FinanceInterface;

class FinanceController extends Controller {

    private $finRepo;
    private $transType = [];
    private $variables = [];    
    private $journals = [];  

    public function __construct(FinanceInterface $finRepo) {
        $this->middleware('guest')->except('logout');
        $this->middleware('checkBackendLeadAccess');
        $this->finRepo = $finRepo;
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
        $this->transType = $this->finRepo->getAllTransType()->get();
        $this->variables = $this->finRepo->getAllVariable()->get();
        $this->journals = $this->finRepo->getAllJournal()->get();
        return view('backend.finance.je_config')
            ->with([
            'transType'=> $this->transType,
            'variables'=> $this->variables,
            'journals'=> $this->journals
            ]);
    }  
}
