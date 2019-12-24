<?php
namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\BusinessInformationRequest;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Contracts\InvoiceInterface as InvoiceInterface;
use App\Inv\Repositories\Models\BizApi;
use Session;
use Helpers;
use App\Libraries\Pdf;


class InvoiceController extends Controller
{
     protected $invRepo;
  
    public function __construct(InvoiceInterface $invRepo){
       $this->invRepo = $invRepo;
      $this->middleware('auth');
      //$this->middleware('checkBackendLeadAccess');
    }
    
    
     /* Invoice upload page  */
     public function getInvoice(){
       
        return view('backend.invoice.uploadinvoice');
        
    }
   
    /* save invoice */
    public function saveInvoice(Request $request)
    {
       $attributes =  $request->all();
       $result  =  $this->invRepo->saveDetails($attributes);
       
        
        
    }
    
}
    