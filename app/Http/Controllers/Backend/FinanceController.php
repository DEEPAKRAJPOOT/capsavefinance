<?php

namespace App\Http\Controllers\Backend;

use Helpers;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\FinanceInterface;
use App\Http\Requests\Backend\CreateJeConfigRequest;

class FinanceController extends Controller {

    private $finRepo;
    private $transType = [];
    private $variables = [];    
    private $journals = [];  
    private $inputData = [];

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

    public function saveJeConfig(CreateJeConfigRequest $request) {
        try {
            $transTypeId = $request->get('trans_type');
            $variables = $request->get('variable');
            $journalId = $request->get('journal');

            $this->inputData = [];
            $this->inputData = [
                'trans_config_id'=>$transTypeId,
                'journal_id'=>$journalId
            ];
            $outputQryJe = $this->finRepo->saveJeData($this->inputData);
            if(isset($outputQryJe->je_config_id)) {
                $this->inputData = [];
                foreach($variables as $key=>$val) {
                    $this->inputData[] = [
                        'trans_config_id'=>$transTypeId,
                        'variable_id'=>$val
                    ];
                }
                $outputQryTransVar = $this->finRepo->saveTransVarData($this->inputData);
            }
            Session::flash('message','Journal entry config saved successfully');
            return redirect()->back();
        } catch (Exception $ex) {
            die;
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
}
