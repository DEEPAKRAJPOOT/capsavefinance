<?php

namespace App\Http\Controllers\Lms;

use File;
use Session;
use Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lms\BankAccountRequest;
use App\Inv\Repositories\Contracts\MasterInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Libraries\Storage\Contract\StorageManagerInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use App\Inv\Repositories\Contracts\Traits\ActivityLogTrait;
use Storage;

class BankAccountController extends Controller {

    //  use ApplicationTrait;
    use ActivityLogTrait;

    protected $appRepo;
    protected $userRepo;
    protected $docRepo;
    protected $master;
    protected $storage;

    /**
     * The pdf instance.
     *
     * @var Pdf
     */
    protected $pdf;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo, MasterInterface $master, StorageManagerInterface $storage)
    {
        $this->appRepo = $app_repo;
        $this->userRepo = $user_repo;
        $this->docRepo = $doc_repo;
        $this->master = $master;
        $this->storage = $storage;
        $this->middleware('checkBackendLeadAccess');
    }

    /**
     * bank account list
     * 
     * @return type mixed
     */
    public function bankAccountList(Request $request)
    {
        try {
            $totalLimit = 0;
            $totalCunsumeLimit = 0;
            $consumeLimit = 0;
            $transactions = 0;
            $user_id = $request->get('user_id');
            $userInfo = $this->userRepo->getCustomerDetail($user_id);
            $bankAccounts = $this->userRepo->getUserBankAccounts($user_id);
            $application = $this->appRepo->getCustomerApplications($user_id);
            $anchors = $this->appRepo->getCustomerPrgmAnchors($user_id);

            foreach ($application as $key => $app) {
                if (isset($app->prgmLimits)) {
                    foreach ($app->prgmLimits as $value) {
                        $totalLimit += $value->limit_amt;
                    }
                }
                if (isset($app->acceptedOffers)) {
                    foreach ($app->acceptedOffers as $value) {
                        $totalCunsumeLimit += $value->prgm_limit_amt;
                    }
                }
            }
            $userInfo->total_limit = number_format($totalLimit);
            $userInfo->consume_limit = number_format($totalCunsumeLimit);
            $userInfo->utilize_limit = number_format($totalLimit - $totalCunsumeLimit);
            return view('lms.customer.bank_account_list')
                    ->with([
                        'userInfo' => $userInfo,
                        'bankAccounts' => $bankAccounts
                        ]);
        } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }

    /**
     * add bank account
     * 
     * @param Request $request
     * @return type mixed
     */
    public function addBankAccount(Request $request)
    {
        try {
            $acc_id = $request->get('bank_account_id');
            $bankAccount = $this->appRepo->getBankAccountData(['bank_account_id' => $acc_id])->first();
            $bank_list = ['' => 'Please Select'] + $this->master->getBankList()->toArray();
            return view('lms.customer.add_bank_account')
                            ->with(['bank_list' => $bank_list, 'bankAccount' => $bankAccount]);
        } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }

    /**
     * Save bank account
     * 
     * @param Request $request
     * @return type mixed
     */
    public function saveBankAccount(BankAccountRequest $request)
    {
       
        try {
            // dd($request->all());
            $acc_id = ($request->get('bank_account_id')) ? \Crypt::decrypt($request->get('bank_account_id')) : null;
            $prepareData = [
                'user_id' => $request->get('user_id'),
                'acc_name' => $request->get('acc_name'),
                'acc_no' => $request->get('acc_no'),
                'bank_id' => $request->get('bank_id'),
                'ifsc_code' => $request->get('ifsc_code'),
                'branch_name' => $request->get('branch_name'),
                'is_active' => $request->get('is_active')
            ];
            if (!empty($_FILES['doc_file']['name']) && empty($_FILES['doc_file']['size'])) {
                 return redirect()->back()->with('error','File not found. Try with another file.');
            }

            $lastInsertId = $this->appRepo->saveBankAccount($prepareData, $acc_id);
            $this->uploadBankDoc($request, $lastInsertId);

            $whereActivi['activity_code'] = 'save_bank_account';
            $activity = $this->master->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Save Bank Account (Manage Sanction Cases) '. null;
                $arrActivity['app_id'] = null;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($prepareData), $arrActivity);
            }             
            
            $messges = $acc_id ? trans('success_messages.update_bank_account_successfully') : trans('success_messages.save_bank_account_successfully');
            Session::flash('message', $messges);
            Session::flash('operation_status', 1);
            return redirect()->back();
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    /**
     * upload bank Doc
     * 
     * @param array $attr
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    function uploadBankDoc($attr, $id)
    {
        try {
            foreach ($attr->file() as $files):
                $docname = $files->getClientOriginalName();
                $ext = $files->getClientOriginalExtension();
                $fileSize = $files->getSize();
                if ($fileSize < config('common.MAX_UPLOAD_SIZE')) {
                    $userBaseDir = 'appDocs/Document/bankDoc/' . auth()->user()->user_id;
                    $userFileName = rand(0, 9999) . time() . '.' . $ext;
                    $pathName = $files->getPathName();
                    Storage::put($userBaseDir . DIRECTORY_SEPARATOR . $userFileName, File::get($pathName));
                    $doc = [
                        'doc_name' => $userFileName,
                        'doc_ext' => $ext,
                        'enc_id' => md5(rand(1, 9999)),
                    ];
                    $this->appRepo->saveBankAccount($doc, $id);
                    File::delete($pathName);
                } else {
                    return redirect()->back()->withErrors(trans('error_messages.file_size_error'));
                }
            endforeach;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * This method is used for see upload file in Bank Account  
     */
    public function seeUploadFile(Request $request) {

        $acc_id = $request->all('bank_account_id');
        $user_id = $request->all('user_id');
    
        $file = $this->appRepo->seeUploadFilePopup($acc_id, $user_id);
        $filePath = 'appDocs/Document/bankDoc/' . auth()->user()->user_id . '/' . $file->doc_name;
        if (Storage::exists($filePath)) {
            $temp_filepath = tempnam(sys_get_temp_dir(), 'file');
            $file_data = Storage::get($filePath);
            file_put_contents($temp_filepath, $file_data);

            return response()
                ->download($temp_filepath, $file->doc_name, [], 'inline')
                ->deleteFileAfterSend();
        }else{
            exit('Requested file does not exist on our server!');
        }
    }

    public function downloadUploadFile(Request $request) {
        $acc_id = $request->all('bank_account_id');
        $user_id = $request->all('user_id');
        $file = $this->appRepo->seeUploadFilePopup($acc_id, $user_id);
        $filePath = 'appDocs/Document/bankDoc/' . auth()->user()->user_id . '/' . $file->doc_name;
        if (Storage::exists($filePath)) {
           return Storage::download($filePath);
        }
        return response(['status' => 'failure', 'message' => 'The file You have Requested to view / Download is not valid.',], 404)
                  ->header('Content-Type', 'application/json');
    }
}


