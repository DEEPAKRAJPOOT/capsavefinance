<?php

namespace App\Http\Controllers\Master;

use Auth;
use Session;
use Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lms\BankAccountRequest;
use App\Http\Requests\Master\CompanyRegRequest;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;

class CompanyController extends Controller {
    
    protected $appRepo;
    protected $masterRepo;

    public function __construct(InvMasterRepoInterface $master, InvAppRepoInterface $app_repo) {
        $this->middleware('auth');
        $this->masterRepo = $master;
        $this->appRepo = $app_repo;
    }

    public function index(Request $request) {
        
        $filter =   [];
        $search_keyword = $request->get('search_keyword');
        $filter['search_keyword'] =   '';
        
        if($search_keyword!='' && $search_keyword!=null){
             $filter['search_keyword'] =$search_keyword;
        }
        $companiesList = $this->masterRepo->getAllCompanies($filter)->toArray();
        return view('master.companies.index',['cmpData' => $companiesList]);
    }

    public function addCompanyForm(Request $request) {

        $company_data = [];
        $company_data['is_active'] = 2;
        
        if (!empty($request->get('id'))) {
            $company_id = preg_replace('#[^0-9]#', '', $request->get('id'));
            $company_data = $this->masterRepo->findCompanyById($company_id);
          
        }
         $get_state_list  =  $this->masterRepo->getAddStateList();
        return view('master.companies.add_companies', ['comData' => $company_data,'state' =>$get_state_list ]);
    }

    public function saveCompanies(CompanyRegRequest $request) {
        try {
            $arrCompaniesData = $request->all();
            //dd($arrCompaniesData);
            $status = false;
            $company_id = false;
            if (!empty($request->get('company_id'))) {
                $company_id = $request->get('company_id');
                $companies_data = $this->masterRepo->findCompanyById($company_id);
                if (!empty($companies_data)) {
                    $arrCompaniesData['updated_by'] = Auth::user()->user_id;
                    $status = $this->masterRepo->updateCompanies($arrCompaniesData, $company_id);
                }
            } else {
                $arrCompaniesData['created_by'] = Auth::user()->user_id;
                $status = $this->masterRepo->saveCompanies($arrCompaniesData);
            }
            if ($status) {
                Session::flash('message', $company_id ? trans('master_messages.company_edit_success') : trans('master_messages.company_add_success'));
                return redirect()->route('get_companies_list');
            } else {
                $companiesList = $this->masterRepo->getAllCompanies();
                Session::flash('error', trans('master_messages.something_went_wrong'));
                return redirect()->route('get_industries_list');
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
    /**
     * add company bank account
     * 
     * @param Request $request
     * @return type mixed
     */
    public function addCompanyBankAccount(Request $request)
    {
        try {
           
            $bankAccount = [];
            $comp_id = $request->get('company_id');
            $bank_acc_id = false;
            $bankAccount['is_default'] = 0;
            
            if (!empty($request->get('bank_account_id'))) {
                $bank_acc_id = preg_replace('#[^0-9]#', '', $request->get('bank_account_id'));
                $bankAccount = $this->appRepo->getBankAccountDataByCompanyId($bank_acc_id,$comp_id)->first();
            }
            
            $bank_list = ['' => 'Please Select'] + $this->masterRepo->getBankList()->toArray();
            return view('master.companies.add_company_bank_account')
                            ->with(['bank_list' => $bank_list, 'companyId' => $comp_id, 'bankAccount' => $bankAccount]);
        } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }
    
    /**
     * Save company bank account
     * 
     * @param Request $request
     * @return type mixed
     */
    public function saveCompanyBankAccount(BankAccountRequest $request)
    {
        try {
//            dd($request->all());
            $by_default = ($request->get('by_default')) ? ((int)$request->get('by_default')) : 0;
            $bank_acc_id = ($request->get('bank_account_id')) ? \Crypt::decrypt($request->get('bank_account_id')) : null;
            $compId = ($request->get('company_id')) ? \Crypt::decrypt($request->get('company_id')) : null;
//            dd($compId);
            $prepareData = [
                'acc_name' => $request->get('acc_name'),
                'acc_no' => $request->get('acc_no'),
                'bank_id' => $request->get('bank_id'),
                'ifsc_code' => $request->get('ifsc_code'),
                'branch_name' => $request->get('branch_name'),
                'is_active' => $request->get('is_active'),
                'user_id' => auth()->user()->user_id,
                'company_id' => $compId,
                'is_default' => $by_default,
            ];

            $this->appRepo->saveBankAccount($prepareData, $bank_acc_id);
            $messges = $bank_acc_id ? trans('success_messages.update_bank_account_successfully') : trans('success_messages.save_bank_account_successfully');
            Session::flash('message', $messges);
            Session::flash('operation_status', 1);
            return redirect()->back();
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

}
