<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\BusinessInformationRequest;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Contracts\InvoiceInterface as InvoiceInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Models\BizApi;
use Session;
use Helpers;
use DB;
use DateTime;
use App\Libraries\Pdf;
use Carbon\Carbon;
use App\Inv\Repositories\Contracts\Traits\InvoiceTrait;

class InvoiceController extends Controller {

    protected $invRepo;
    protected $docRepo;
    protected $application;
    public function __construct(InvoiceInterface $invRepo, InvDocumentRepoInterface $docRepo, InvAppRepoInterface $application) {
        $this->invRepo = $invRepo;
        $this->docRepo = $docRepo;
        $this->application  =  $application;
        $this->middleware('auth');
        //$this->middleware('checkBackendLeadAccess');
    }

    /* Invoice upload page  */

    public function getInvoice(Request $request) {
        
        $anchor_id = $request->anchor_id;
        $user_id   = $request->user_id;
        $app_id    = $request->app_id;
        $biz_id    = $request->biz_id;
        $get_user = $this->invRepo->getUser($user_id);
        $get_anchor = $this->invRepo->getLimitAnchor($app_id);
        if(count($get_anchor)==1)
        {
              $getAid  = $get_anchor[0]->anchorList->anchor_id;
              $get_program = $this->invRepo->getLimitProgram($getAid);
            
        }
        else {
             $get_program = [];
        }
      
         return view('frontend.application.invoice.uploadinvoice')
                   ->with(['get_user' => $get_user,'get_program' => $get_program, 'get_anchor' => $get_anchor, 'app_id' => $app_id,'biz_id' => $biz_id]);
    }
    
    public function getAllInvoice()
    {
        
        $get_anchor = $this->invRepo->getLimitAllAnchor();
        return view('frontend.application.invoice.upload_all_invoice')
                   ->with(['get_anchor' => $get_anchor]);
  
    }
  
      public function getBulkInvoice() {
          
         $getAllInvoice    =   $this->invRepo->getLimitAllAnchor();
         $get_bus = $this->invRepo->getBusinessName();  
         $getBulkInvoice = $this->invRepo->getUserAllBulkInvoice();
         return view('frontend.application.invoice.bulk_invoice')->with(['get_bus' => $get_bus, 'anchor_list'=> $getAllInvoice,'getBulkInvoice' =>$getBulkInvoice]);
        
      } 
    
         public function viewInvoice(Request $req) {
                $flag = $req->get('flag') ?: null;
                $user_id = $req->get('user_id') ?: null;
                $app_id = $req->get('app_id') ?: null;
                $userInfo = $this->invRepo->getCustomerDetail($user_id);
                $getAllInvoice = $this->invRepo->getAllInvoiceAnchor(7);
                $get_bus = $this->invRepo->getUserBusinessNameApp(7);
                $status =  DB::table('mst_status')->where(['status_type' =>4])->get();
                return view('frontend.application.invoice.invoice')->with(['get_bus' => $get_bus, 'anchor_list' => $getAllInvoice, 'flag' => $flag, 'user_id' => $user_id, 'app_id' => $app_id, 'userInfo' => $userInfo,'status' =>$status]);
  
        }
    
    /* get suplier & program b behalf of anchor id */
      public function getProgramSupplier(Request $request){
       $attributes = $request->all();
       $invId   = $attributes['anchor_id'];
       $get_user  =   $this->invRepo->getUserBehalfAnchor($invId);
       return response()->json(['status' => 1,'userList' =>$get_user]);

      }
      
      
      /* failed invoice status iframe    */
       public function invoiceFailedStatus(Request $request){
           dd( $request->invoice_id);
         return view('frontend.application.invoice.invoice_failed_status');
      }
      
       /* success invoice status iframe    */
       public function invoiceSuccessStatus(Request $request){
          $result =  $this->invRepo->getDisbursedAmount($request->get('invoice_id'));
         return view('frontend.application.invoice.invoice_success_status')->with(['result' => $result]);
      }
         /* success invoice status iframe    */
       public function viewInvoiceDetails(Request $request){
            $invoice_id = $request->get('invoice_id');
            $res = $this->invRepo->getSingleInvoice($invoice_id);
            $get_status = DB::table('mst_status')->where('status_type', 4)->get();
            return view('frontend.application.invoice.view_invoice_details')->with(['invoice' => $res, 'status' => $get_status]);
      }
      
        /* update invoice amount  */
      public function saveInvoiceAmount(Request $request)
      {     $id = Auth::user()->user_id;
            $attributes = $request->all();
            $res =  $this->invRepo->updateInvoiceAmount($attributes);
           if($res)
           {
                
                  Session::flash('message', 'Invoice Amount successfully Updated');
                  return back();
           }
        else {
               Session::flash('message', 'Something wrong, Amount is not Updated');
               return back();
          }
      }
      
     /*   save invoice */

    public function saveInvoice(Request $request) {
        $attributes = $request->all();
        $explode = explode(',', $attributes['supplier_id']);
        $attributes['supplier_id'] = $explode[0];
        $appId = $explode[1];
        $date = Carbon::now();
        $id = Auth::user()->user_id;
        $res = $this->invRepo->getSingleAnchorDataByAppId($appId);
        $biz_id = $res->biz_id;
        if ($attributes['exception']) {
            $statusId = 28;
        } else {
            $statusId = 7;
        }

        $uploadData = Helpers::uploadAppFile($attributes, $appId);
        $userFile = $this->docRepo->saveFile($uploadData);
        $invoice_approve_amount = str_replace(",", "", $attributes['invoice_approve_amount']);
        $invoice_amount = str_replace(',', '', $attributes['invoice_approve_amount']);
        $arr = array('anchor_id' => $attributes['anchor_id'],
            'supplier_id' => $attributes['supplier_id'],
            'program_id' => $attributes['program_id'],
            'app_id' => $appId,
            'biz_id' => $biz_id,
            'invoice_no' => $attributes['invoice_no'],
            'tenor' => $attributes['tenor'],
            'invoice_due_date' => ($attributes['invoice_due_date']) ? Carbon::createFromFormat('d/m/Y', $attributes['invoice_due_date'])->format('Y-m-d') : '',
            'invoice_date' => ($attributes['invoice_date']) ? Carbon::createFromFormat('d/m/Y', $attributes['invoice_date'])->format('Y-m-d') : '',
            'invoice_approve_amount' => $invoice_approve_amount,
            'invoice_amount' => $invoice_amount,
            'prgm_offer_id' => $attributes['prgm_offer_id'],
            'status_id' => $statusId,
            'remark' => $attributes['remark'],
            'file_id' => $userFile->file_id,
            'created_by' => $id,
            'created_at' => $date);
            $result = $this->invRepo->save($arr);

        if ($result) {

            $this->invRepo->saveInvoiceStatusLog($result, $statusId);
            Session::flash('message', 'Invoice successfully saved');
            return back();
        } else {
            Session::flash('message', 'Something wrong, Invoice is not saved');
            return back();
        }
    }
    
    
    
    public function saveBulkInvoice(Request $request)
    {  
        $getCustomerId = $this->invRepo->checkLmsUser();
        if(!$getCustomerId)
        {
             Session::flash('error','Customer Limit is not sanctioned or offer is not approved.');
             return back(); 
        }
        $date = Carbon::now();
        $id = Auth::user()->user_id; 
        $attributes = $request->all();
        $program_name  = explode(',',$attributes['program_name']);
        $prgm_id        =   $program_name[0];
        $prgm_limit_id   =   $program_name[1];
        $batch_id =  self::createBatchNumber(6);
        $uploadData = Helpers::uploadInvoiceFile($attributes, $batch_id); 
      if($uploadData['status']==0)
        {
             Session::flash('error', $uploadData['message']);
             return back(); 
        }
        $userFile = $this->docRepo->saveFile($uploadData);  ///Upload csv
        $userFile['batch_no'] =  $batch_id;
        if($userFile)
       {
           $resFile =  $this->invRepo->saveInvoiceBatch($userFile);
           if($resFile)
           {
              $uploadData = Helpers::uploadZipInvoiceFile($attributes, $batch_id); ///Upload zip file
              if($uploadData['status']==0)
             {
               Session::flash('error', $uploadData['message']);
               return back(); 
             }
              if($uploadData)
              {   
                  $zipBatch  =   self::createBatchNumber(6);
                  $uploadData['batch_no'] = $zipBatch;
                  $uploadData['parent_bulk_batch_id'] =  $resFile->invoice_bulk_batch_id;
                  $resZipFile =  $this->invRepo->saveInvoiceZipBatch($uploadData);
                  if($resZipFile)
                  {
                    $csvPath = storage_path('app/public/'.$userFile->file_path);
                    $handle = fopen($csvPath, "r");
                    $data = fgetcsv($handle, 1000, ",");
                    if(count($data) < 4 || count($data) > 4)
                    {
                       $multichk['multiVali1'] = 'Please check Csv file format';
                       Session::flash('multiVali',$multichk);
                       return back();
                    }
                    $csvPath1 = storage_path('app/public/'.$userFile->file_path);
                    $handle1 = fopen($csvPath1, "r");
                    $data1 = fgetcsv($handle1, 1000, ",");
                    $key=0;
                    $ins = [];
                    $dataAttr[] ="";
                   $multiValiChk =  InvoiceTrait::multiValiChk($handle1,$prgm_id,$attributes['anchor_name'],$getCustomerId['customer_id']);
               
                if($multiValiChk['status']==0)
                {

                    Session::flash('multiVali', $multiValiChk);
                    return back();   
                }
            
                  while(($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
                    {   
                      
                        $inv_no  =   $data[0]; 
                        $inv_date  =   $data[1]; 
                        $amount  =   $data[2]; 
                        $file_name  =   $data[3];
                        $dataAttr['cusomer_id']  =   $getCustomerId['customer_id']; 
                        $dataAttr['inv_no']  =   $data[0]; 
                        $dataAttr['inv_date']  =   $data[1]; 
                        $dataAttr['amount']  =   $data[2]; 
                        $dataAttr['file_name']  =   $data[3];
                        $dataAttr['anchor_id']  =   $attributes['anchor_name'];
                        $dataAttr['prgm_id']  =   $prgm_id;
                        $chlLmsCusto  = InvoiceTrait::getLimitProgram($dataAttr);
                        $getPrgm  = $this->application->getProgram($prgm_id);
                        if($chlLmsCusto['status']==0)
                        {
                           Session::flash('error', $chlLmsCusto['message']);
                           return back(); 
                        }
                        ////// for validation paramiter here//////
                        $dataAttr['user_id']  =  $chlLmsCusto['user_id'];
                        $dataAttr['app_id']  =   $chlLmsCusto['app_id'];
                        $dataAttr['biz_id']  =   $chlLmsCusto['biz_id'];
                        $dataAttr['tenor']  =   $chlLmsCusto['tenor'];
                        $dataAttr['old_tenor']  =   $chlLmsCusto['tenor_old_invoice'];
                        $dataAttr['prgm_offer_id']  =   $chlLmsCusto['prgm_offer_id'];
                        $dataAttr['approval']  =   $getPrgm;
                        $getInvDueDate =  InvoiceTrait::getInvoiceDueDate($dataAttr); /* get invoice due date*/
                        if($getInvDueDate['status']==0)
                        {
                           Session::flash('error', $getInvDueDate['message']);
                           return back(); 
                        }
                        $dataAttr['inv_due_date']  =   $getInvDueDate['inv_due_date']; 
                        $error = InvoiceTrait::checkCsvFile($dataAttr);
                       
                        if($error['status']==0)
                        {
                           Session::flash('error', $error['message']);
                           return back(); 
                        }
                        $status_id =  $error['status_id'];
                        $comm_txt  =  $error['message'];
                        $error  =  $error['error'];
                      if($file_name)
                      {
                        $getImage =  Helpers::ImageChk($file_name,$batch_id);
                       if($getImage['status']==1)
                        {
                          
                           $FileDetail = $this->docRepo->saveFile($getImage); 
                           $FileId  = $FileDetail->file_id; 
                        }
                        else
                        {
                           $FileId  = Null; 
                           $comm_txt  =  $getImage['message']; 
                        }
                         
                      }
                      else
                      {
                            $FileId  = Null; 
                           
                      }
                        
                        $userLimit = $chlLmsCusto['limit'];
                        $ins['anchor_id'] = $attributes['anchor_name'];
                        $ins['supplier_id'] = $dataAttr['user_id'];
                        $ins['program_id'] = $prgm_id;
                        $ins['prgm_offer_id'] = $dataAttr['prgm_offer_id'];
                        $ins['app_id'] = $dataAttr['app_id'];
                        $ins['biz_id'] = $dataAttr['biz_id'];
                        $ins['invoice_no'] = $inv_no;
                        $ins['tenor'] = $dataAttr['tenor'];
                        $ins['invoice_date'] = Carbon::createFromFormat('d-m-Y', $inv_date)->format('Y-m-d');
                        $ins['invoice_due_date'] = Carbon::createFromFormat('d-m-Y', $dataAttr['inv_due_date'])->format('Y-m-d');
                        $ins['pay_calculation_on'] = 2;
                        $ins['invoice_approve_amount'] = $amount;
                        $ins['comm_txt'] = $comm_txt;
                        $ins['status'] = $error;
                        $ins['status_id'] = $status_id;
                        $ins['file_id'] =  $FileId;
                        $ins['created_by'] =  $id;
                        $ins['created_at'] =  $date;
                        $key++;
                      
                        $res =  $this->invRepo->saveInvoice($ins);
                        if($res)
                        {
                           
                            if($res['status']!=2)
                            {
                               InvoiceTrait::updateLimit($status_id,$userLimit,$amount,$dataAttr,$res->invoice_bulk_upload_id);  
                            }
                         
                        }
                           
                    } 
            
                     
                         Session::flash('message', 'Invoice data successfully sent to under reviewer process');
                         return back();  
                     
                  }
              }
           }
       }
      
    }
    
   

    public static function createBatchNumber($length = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    

}
