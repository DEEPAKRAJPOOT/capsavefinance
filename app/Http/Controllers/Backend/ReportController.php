<?php

namespace App\Http\Controllers\Backend;
use Auth;
use Session;
use Helpers;
use PDF as DPDF;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;
use PHPExcel_Cell_DataType;
use PHPExcel_Style_Alignment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Inv\Repositories\Contracts\InvoiceInterface as InvoiceInterface;
use App\Inv\Repositories\Models\LmsUser;
class ReportController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $invRepo;
    public function __construct(InvoiceInterface $invRepo) {
        $this->invRepo = $invRepo;
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
     public function realisationreport(Request $request) {
        try {
            return view('reports.realisationreport  ');
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
        
    } 
    public function pdfInvoiceDue(Request $request)
    {
        $user = LmsUser::where('customer_id',$request->customer_id)->pluck('user_id');
        $getInvoice  =  $this->invRepo->pdfInvoiceDue($request);
        DPDF::setOptions(['isHtml5ParserEnabled'=> true]);
        $pdf = DPDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'defaultPaperSize' => 'a4'])
                ->loadView('reports.downloadinvoicedue', ['userInfo' => $getInvoice, 'fromdate' => $request->from_date, 'todate' => $request->to_date,'user' => $user],[],'UTF-8');
        return $pdf->download('InvoiceDueReport.pdf');  
    }
    
     public function pdfInvoiceOverDue(Request $request)
    {
        $user = LmsUser::where('customer_id',$request->customer_id)->pluck('user_id');
        $getInvoice  =  $this->invRepo->pdfInvoiceOverDue($request);
        DPDF::setOptions(['isHtml5ParserEnabled'=> true]);
        $pdf = DPDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'defaultPaperSize' => 'a4'])
                ->loadView('reports.downloadinvoiceoverdue', ['userInfo' => $getInvoice, 'fromdate' => $request->from_date, 'todate' => $request->to_date,'user' => $user],[],'UTF-8');
        return $pdf->download('InvoiceOverDueReport.pdf');  
    } 
    
   public function pdfInvoiceRealisation(Request $request)
   {
        $user = LmsUser::where('customer_id',$request->customer_id)->pluck('user_id');
        $getInvoice  =  $this->invRepo->pdfInvoiceRealisation($request);
        DPDF::setOptions(['isHtml5ParserEnabled'=> true]);
        $pdf = DPDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'defaultPaperSize' => 'a4'])
                ->loadView('reports.downloadrealisation', ['userInfo' => $getInvoice, 'fromdate' => $request->from_date, 'todate' => $request->to_date,'user' => $user],[],'UTF-8');
        return $pdf->download('InvoiceRealisation.pdf');    
   }
}