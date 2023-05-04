<?php

namespace App\Jobs;

use PDF;
use Exception;
use App\Helpers\FileHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Models\LmsUser;
use Illuminate\Queue\InteractsWithQueue;
use App\Inv\Repositories\Models\UserFile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Inv\Repositories\Models\Lms\UserInvoice;
use App\Inv\Repositories\Contracts\ReportInterface;
use App\Inv\Repositories\Models\Master\EmailTemplate;
use App\Inv\Repositories\Contracts\UserInvoiceInterface;


class GenerateNotePdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $userInvoice_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userInvoice_id)
    {
        $this->userInvoice_id = $userInvoice_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(UserInvoiceInterface $UserInvRepo, FileHelper $file_helper)
    {
        $job_id = $this->job->getJobId();
        UserInvoice::where('user_invoice_id',$this->userInvoice_id)->update(['job_id' => $job_id]);
        $this->UserInvRepo = $UserInvRepo;
        $this->fileHelper = $file_helper;
        $userInvoiceId = $this->userInvoice_id;
        if($userInvoiceId){
            $invData = $this->UserInvRepo->getInvoiceById($userInvoiceId);
            $user_id = $invData->user_id;
            $reference_no = $invData->reference_no;
            $invoice_no = $invData->invoice_no;
            $state_name = $invData->place_of_supply;
            $invoice_type = $invData->invoice_type;
            $invoiceBorneBy = $invData->invoice_borne_by;
            $invoice_type_name = $invData->invoice_type_name;
            $invoice_date = $invData->invoice_date;
            $due_date = $invData->due_date;
            $company_id = $invData->comp_addr_id;
            $registered_comp_id = $invData->registered_comp_id;
            $bank_account_id = $invData->bank_id;
            $totalTxnsInInvoice = $invData->userInvoiceTxns;
            $userStateId = $invData->user_gst_state_id;
            $companyStateId = $invData->comp_gst_state_id;
            $lmsDetails = LmsUser::getLmsDetailByUserId($user_id);
            $virtual_acc_id = $lmsDetails[0]->virtual_acc_id;
            $stateDetail = $this->UserInvRepo->getStateById($userStateId);
            unset($lmsDetails);
            $billingDetails = [
                'name' => $invData->biz_entity_name,
                'address' => $invData->gst_addr,
                'pan_no' => $invData->pan_no,
                'state_id' => $stateDetail->id,
                'state_name' => $stateDetail->name,
                'state_no' => $stateDetail->state_no,
                'state_code' => $stateDetail->state_code,
                'gstin_no' => $invData->biz_gst_no,
                'biz_gst_state_code' => $invData->biz_gst_state_code,
            ];
            $origin_of_recipient = [
                'reference_no' => $reference_no,
                'invoice_no' => $invoice_no,
                'place_of_supply' => $state_name,
                'invoice_date' => $invoice_date,
                'due_date' => $due_date,
                'virtual_acc_id' => $virtual_acc_id,
            ];
            if (empty($invData->inv_comp_data)) {
                $companyDetail = $this->_getCompanyDetail($company_id, $bank_account_id);
                if ($companyDetail['status'] != 'success') {
                    throw new Exception($companyDetail['message']);
                }
                $company_data = $companyDetail['data'];
            }else{
                $company_data = json_decode($invData->inv_comp_data, true);
            }
            $is_state_diffrent = ($userStateId != $companyStateId);
            $intrest_charges = [];
            $total_sum_of_rental = 0;
            foreach ($totalTxnsInInvoice as  $key => $invTrans) {
                $igst_amt = $invTrans->igst_amount;
                $igst_rate = $invTrans->igst_rate;
                $cgst_amt = $invTrans->cgst_amount;
                $cgst_rate = $invTrans->cgst_rate;
                $sgst_amt = $invTrans->sgst_amount;
                $sgst_rate = $invTrans->sgst_rate;
                $base_amt = $invTrans->base_amount;
                $sac_code = $invTrans->sac_code;

                $intrest_charges[$key] = array(
                    'trans_id' => $invTrans->trans_id,
                    'desc' => $invTrans->description,
                    'sac' => $sac_code,
                    'base_amt' => round($base_amt,2),
                    'sgst_rate' => ($sgst_rate != 0 ? $sgst_rate : 0),
                    'sgst_amt' => ($sgst_amt != 0 ? $sgst_amt : 0),
                    'cgst_rate' => ($cgst_rate != 0 ? $cgst_rate : 0),
                    'cgst_amt' =>  ($cgst_amt != 0 ? $cgst_amt : 0),
                    'igst_rate' => ($igst_rate != 0 ? $igst_rate : 0),
                    'igst_amt' =>  ($igst_amt != 0 ? $igst_amt : 0),
                    'trans_date' => $invTrans->settle_payment_desc,
                );
                $total_rental = round($base_amt + $sgst_amt + $cgst_amt + $igst_amt, 2);
                $total_sum_of_rental += $total_rental; 
                $intrest_charges[$key]['total_rental'] =  $total_rental; 
            }
            $registeredCompany = json_decode($invData->comp_addr_register, true);
            $data = [
                'company_data' => $company_data,
                'billingDetails' => $billingDetails,
                'origin_of_recipient' => $origin_of_recipient, 
                'intrest_charges' => $intrest_charges,
                'total_sum_of_rental' => $total_sum_of_rental,
                'registeredCompany' => $registeredCompany,
                'invoice_type'=>$invoice_type,
                'invoice_type_name' => $invoice_type_name
            ];

            if($invoiceBorneBy == '1'){
                $data['custId'] = $invData->customer_id ?? '';
                $data['custName'] = $invData->customer_name ?? '';
            }

            view()->share($data);
            $path ='capsaveInvoice/'.str_replace("/","_",strtoupper($data['origin_of_recipient']['invoice_no'])).'.pdf';
            //$year = date("Y");   
            //$month = date("m");
            //$path ='capsaveInvoice/'.$year.'/'.$month.'/'.str_replace("/","_",strtoupper($data['origin_of_recipient']['invoice_no'])).'.pdf';
            if(Storage::exists('public/'.$path)){
                Storage::move('public/'.$path, 'public/'.'capsaveInvoice/'.str_replace("/","_",strtoupper($data['origin_of_recipient']['invoice_no'])).'_'.time().'.pdf');
            }
            switch ($invData->invoice_cat) {
                case '1':
                    $pdf = PDF::loadView('lms.note.generate_debit_note');
                    break;
                case '2':
                    $pdf = PDF::loadView('lms.note.generate_credit_note');
                    break;
            }
            
            if($pdf){
                $fileData =  $this->fileHelper->uploadFileWithContent($path,$pdf->output());
                $userFile = UserFile::create($fileData);
                if($userInvoiceId && $userFile){
                    UserInvoice::where('user_invoice_id',$userInvoiceId)->update(['file_id' => $userFile->file_id]);
                }
            }
        }

    }
}
