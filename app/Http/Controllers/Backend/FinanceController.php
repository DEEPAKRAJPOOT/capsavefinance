<?php

namespace App\Http\Controllers\Backend;

use Helpers;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\FinanceInterface;
use App\Http\Requests\Backend\CreateJeConfigRequest;
use App\Http\Requests\Backend\CreateJiConfigRequest;
use App\Http\Requests\Backend\CreateJournalRequest;
use App\Http\Requests\Backend\CreateAccountRequest;
use App\Helpers\FinanceHelper;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;
use PHPExcel_Cell_DataType;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;

class FinanceController extends Controller {

    private $finRepo;
    private $transType = [];
    private $variables = [];    
    private $journals = [];  
    private $accounts = [];
    private $inputData = [];

    public function __construct(FinanceInterface $finRepo) {
        $this->middleware('guest')->except('logout');
        $this->middleware('checkBackendLeadAccess');
        $this->finRepo = $finRepo;
    }


    public function getFinTransList() {
        return view('backend.finance.trans_list');
    }    

    public function getFinJournal(Request $request) {
        $journalData = '';
        $journalId = $request->get('journal_id');
        if(isset($journalId) && !empty($journalId)){
            $journalData = $this->finRepo->getJournalByJournalId($journalId); 
        }
        return view('backend.finance.journal_list')
            ->with([
            'journalData'=> $journalData,
            'journalId'=> $journalId
            ]);
    }  

    public function getFinAccount(Request $request) {
        $accountData = '';
        $accountId = $request->get('account_id');
        if(isset($accountId) && !empty($accountId)){
            $accountData = $this->finRepo->getAccountByAccountId($accountId); 
        }
        return view('backend.finance.account_list')
            ->with([
            'accountData'=> $accountData,
            'accountId'=> $accountId
            ]);;;
    }    

    public function getFinVariable() {
        return view('backend.finance.variable_list');
    }
    
    public function exportTransactions(Request $request) {
        // $voucher_type = $request->get('type');
        // if (!in_array($voucher_type, [1,2])) {
        //     Session::flash('error','Invalid voucher type found.');
        //     return back();
        // }
        // 1 for journal and 2 for bank
        $where = [];
        $result = $this->finRepo->getTallyTxns();
         $records = [];
         $header[] = [
            "batch_no" => "Batch No",
            "entry_type" => "Entry Type",
            "voucher_type" => "Voucher Type",
            "voucher_code" => "Voucher Code",
            "voucher_date" => "Voucher Date",
            "invoice_no" => "Invoice No",
            "invoice_date" => "Invoice Date",
            "ledger_name" => "Ledger Name",
            "amount" => "Amount",
            "ref_no" => "Reference No",
            "ref_amount" => "Reference Amount",
            "acc_no" => "Account No",
            "ifsc_code" => "IFSC Code",
            "bank_name" => "Bank Name",
            "cheque_amount" => "Cheque Amount",
            "cross_using" => "Cross Using",
            "trans_date" => "Trans Date",
            "trans_type" => "Transaction Type",
            "inst_no" => "Inst No",
            "inst_date" => "Inst Date",
            "favoring_name" => "Favoring Name",
            "remarks" => "Remarks",
            "narration" => "Narration",
            "is_posted" => "Is Posted",
            ];
        foreach ($result as $key => $value) {
            $fetchedArr = (array)$value;
            $voucherDate = date('Y, d F',strtotime($fetchedArr['voucher_date']));
            if (strtolower($fetchedArr['voucher_type']) == 'payment') {
                $records['PAYMENT'][] =  [
                    "voucher_no" => $fetchedArr['voucher_code'],
                    "voucher_type" => $fetchedArr['voucher_type'],
                    "voucher_date" => $voucherDate,
                    "ledger_name" => $fetchedArr['ledger_name'],
                    "amount" => $fetchedArr['amount'],
                    "dr_/_cr" => $fetchedArr['entry_type'],
                    "reference_no" => $fetchedArr['ref_no'],
                    "reference_amount" => $fetchedArr['ref_amount'],
                    "transaction_type" => $fetchedArr['mode_of_pay'],
                    "a_/_c_no" => $fetchedArr['acc_no'],
                    "ifsc_code" => $fetchedArr['ifsc_code'],
                    "bank_name" => $fetchedArr['bank_name'],
                    "cheque_amount" => $fetchedArr['cheque_amount'],
                    "cross_using" => $fetchedArr['cross_using'],
                    "inst_no" => $fetchedArr['inst_no'],
                    "inst_date" => $fetchedArr['inst_date'],
                    "favoring_name" => $fetchedArr['favoring_name'],
                    "narration" => $fetchedArr['narration'],
                    "is_posted" => $fetchedArr['is_posted'],
                ];
            }else{
                $records['JOURNAL'][] = [
                    "batch_no" => $fetchedArr['batch_no'],
                    "voucher_no" => $fetchedArr['voucher_code'],
                    "voucher_type" => $fetchedArr['voucher_type'],
                    "voucher_date" => $voucherDate,
                    "dr_ledger_name" => $fetchedArr['trans_type'],
                    "dr_amount" => $fetchedArr['amount'],
                    "ref_no" => $fetchedArr['ref_no'],
                    "ref_amount" => $fetchedArr['amount'],
                    "cr_ledger_name" => $fetchedArr['ledger_name'],
                    "cr_amount" => $fetchedArr['amount'],
                    "cr_ref_no" => $fetchedArr['ref_no'],
                    "cr_ref_amount" => $fetchedArr['amount'],
                    "narration" => $fetchedArr['narration'], 
                ]; 
            }
        }
        $toExportData = $records;
        $this->array_to_excel($toExportData, "execl.xlsx");
    }

    public function crateJeConfig(Request $request) {
        $variablesIdArray = [];
        $jeConfigId = $request->get('je_config_id');
        $transConfigId = $request->get('trans_config_id');
        $journalId = $request->get('journal_id');
        $this->transType = $this->finRepo->getAllTransType()->get();
        $this->variables = $this->finRepo->getAllVariable()->get();
        $this->journals = $this->finRepo->getAllJournal()->get();
        if(isset($jeConfigId) && !empty($jeConfigId)){
            $jeVariablesData = $this->finRepo->getVariablesByTransConfigId($transConfigId); 
            if($jeVariablesData[0]->variables->count()>0) {
                foreach($jeVariablesData[0]->variables as $key=>$val){
                    $variablesIdArray[] = $val->variable_id;
                }
            }          
        }
        return view('backend.finance.je_config')
            ->with([
            'transType'=> $this->transType,
            'variables'=> $this->variables,
            'journals'=> $this->journals,
            'jeConfigId'=> $jeConfigId,
            'transConfigId'=> $transConfigId,
            'journalId'=> $journalId,
            'variablesIdArray'=>$variablesIdArray
            ]);
    }  

    public function saveJeConfig(CreateJeConfigRequest $request) {
        try {
            $jeConfigId = $request->get('jeConfigId');
            $transTypeId = $request->get('trans_type');
            $variables = $request->get('variable');
            $journalId = $request->get('journal');

            $this->inputData = [];
            if(isset($jeConfigId) && !empty($jeConfigId)){
                $transConfigId = $request->get('transConfigId');
                foreach($variables as $key=>$val) {
                    $this->inputData[] = $val;
                }
                $outputQryTransVar = $this->finRepo->syncTransVarData($this->inputData, $transConfigId);
            } else {
                $this->inputData = [];
                $this->inputData = [
                    'trans_config_id'=>$transTypeId,
                    'journal_id'=>$journalId
                ];

                $recCount = $this->finRepo->checkTransJeData($transTypeId, $journalId);
                if($recCount >0){
                    Session::flash('error','Journal entry already exist for this transaction type.');
                    return redirect()->back();
                }
                $outputQryJe = $this->finRepo->saveJeData($this->inputData);
                if(isset($outputQryJe->je_config_id)) {
                    $this->inputData = [];
                    // foreach($variables as $key=>$val) {
                    //     $this->inputData[] = [
                    //         'trans_config_id'=>$transTypeId,
                    //         'variable_id'=>$val
                    //     ];
                    // }
                    // $outputQryTransVar = $this->finRepo->saveTransVarData($this->inputData);
                    foreach($variables as $key=>$val) {
                        $this->inputData[] = $val;
                    }
                    $outputQryTransVar = $this->finRepo->syncTransVarData($this->inputData, $transTypeId);                
                }
            }
            Session::flash('message','Journal entry config saved successfully');
            return redirect()->back();
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function addJiConfig(Request $request) {
        try {
            $jiConfigData = null;
            $jiConfigId = $request->get('ji_config_id');
            if(isset($jiConfigId) && !empty($jiConfigId)){
                $jiConfigData = $this->finRepo->getJiConfigByjiConfigId($jiConfigId); 
            }
            $jeConfigId = $request->get('je_config_id');       
            $this->accounts = $this->finRepo->getAllAccount()->get();      
            $jeConfigData = $this->finRepo->getJeConfigByjeConfigId($jeConfigId);       
            if(isset($jeConfigData->je_config_id) && !empty($jeConfigData->je_config_id)) {
                $this->variables = explode(',', $jeConfigData->variable_name);
            }
            return view('backend.finance.ji_config')
                ->with([
                'jeConfigId'=> $jeConfigId,
                'variables'=> $this->variables,
                'accounts' => $this->accounts,
                'jiConfigData' => $jiConfigData
                ]);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }       
    }

    public function saveJiConfig(CreateJiConfigRequest $request) {
        try {
            $jiConfigId = $request->get('ji_config_id');
            $this->inputData = [];
            $this->inputData = [                
                'account_id'=>$request->get('account'),
                'is_partner'=>$request->get('is_partner'),
                'label'=>$request->get('label'),                
                'value_type'=>$request->get('value_type'),
                'config_value'=>$request->get('config_value'),
                'je_config_id'=>$request->get('je_config_id')
            ];

            if(isset($jiConfigId) && !empty($jiConfigId)){
                $outputQryJi = $this->finRepo->saveJiData($this->inputData, $jiConfigId);
                if(isset($outputQryJi)) {
                    Session::flash('message','Journal item updated successfully');
                } else {
                    Session::flash('error','Journal item not updated, Please try later.');
                }   
            } else {
                $outputQryJi = $this->finRepo->saveJiData($this->inputData, null);
                if(isset($outputQryJi->ji_config_id)) {
                    Session::flash('message','Journal item saved successfully');
                } else {
                    Session::flash('error','Journal item not saved, Please try later.');
                }   
            }
            return redirect()->back();                      
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function saveJournal(CreateJournalRequest $request) {
        try {
            $journalId = $request->get('journalId');
            $this->inputData = [];
            $this->inputData = [                
                'name'=>$request->get('name'),               
                'journal_type'=>$request->get('journal_type'),
                'is_active'=>$request->get('is_active')
            ];

            if(isset($journalId) && !empty($journalId)){
                $outputQry = $this->finRepo->saveJournalData($this->inputData, $journalId);
                if(isset($outputQry)) {
                    Session::flash('message','Journal updated successfully');
                } else {
                    Session::flash('error','Journal not updated, Please try later.');
                }   
            } else {
                $outputQry = $this->finRepo->saveJournalData($this->inputData, null);
                if(isset($outputQry->id)) {
                    Session::flash('message','Journal saved successfully');
                } else {
                    Session::flash('error','Journal not saved, Please try later.');
                }   
            }
            return redirect()->back();                      
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function saveAccount(CreateAccountRequest $request) {
        try {
            $accountId = $request->get('accountId');
            $this->inputData = [];
            $this->inputData = [                
                'account_code'=>$request->get('account_code'),               
                'account_name'=>$request->get('account_name'),
                'is_active'=>$request->get('is_active')
            ];

            if(isset($accountId) && !empty($accountId)){
                $outputQry = $this->finRepo->saveAccountData($this->inputData, $accountId);
                if(isset($outputQry)) {
                    Session::flash('message','Account updated successfully');
                } else {
                    Session::flash('error','Account not updated, Please try later.');
                }   
            } else {
                $outputQry = $this->finRepo->saveAccountData($this->inputData, null);
                if(isset($outputQry->id)) {
                    Session::flash('message','Account saved successfully');
                } else {
                    Session::flash('error','Account not saved, Please try later.');
                }   
            }
            return redirect()->back();                      
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function getFinTransactions() {
        return view('backend.finance.transactions');
    }

    public function array_to_csv($array, $download = "") {
        if ($download != ""){    
            header('Content-Type: application/csv');
            header('Content-Disposition: attachement; filename="' . $download . '"');
        }
        ob_start();
        $f = fopen('php://output', 'w') or die("Can't open php://output");
        foreach ($array as $line){
            if (!fputcsv($f, $line,"\t")){
               continue;
            }
        }
        $str = ob_get_contents();
        fclose($f);
        ob_end_clean();
        if ($download == ""){
            return $str;    
        }else{    
            echo $str;
        }        
    }

    public function array_to_excel($toExportData, $file_name = "") {
        ob_start();
        // if(empty($file_name)) {
            $file_name = "Report - " . _getRand(15).".xlsx";
        // }
        $activeSheet = 0;
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->createSheet();
        foreach ($toExportData as $title => $data) {
            $rec_count = count($data[0]);
            $header_cols = array_keys($data[0]);
            $sheetTitle = $title;
            $objPHPExcel->setActiveSheetIndex($activeSheet);
            $activeSheet++;
            $column = 0;
            $header_row = 2;
            $start_row = 4;
            $row = $start_row;
            $column = 0;
            $floor = floor($rec_count/26);
            $reminder = $rec_count % 26;
            $char = ($floor > 0 ? chr(ord("A") + $floor - 1) : '').chr(ord("A") + $reminder - 1);
            foreach($data as $key => $item) {
              foreach($item as $key1 => $item1) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $row, $item1);
                $column++;
              }
              $argb = "FFFFFFFF";
              if ($row % 2 == 1){
                $argb = "FFE0E0E0";
              }
              $styleArray = array(
                'fill' => array(
                  'type' => PHPExcel_Style_Fill::FILL_SOLID,
                  'startcolor' => array(
                    'argb' => $argb,
                  ),
                ),
              );
              $objPHPExcel->getActiveSheet()->getStyle('A'. $row .':' . $char . $row)->applyFromArray($styleArray);
              $column = 0;
              $row++;
            }
            $end_row = $row - 1;
            $row = $header_row;
            $column = 0;
            foreach($header_cols as $key) {
               $key = ucwords(str_replace('_', ' ', $key));
               $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($column, $row)->setValueExplicit($key, PHPExcel_Cell_DataType::TYPE_STRING);
                  $column++;
            }
            $styleArray = array(
              'font' => array(
                'bold' => true,
              ),
              'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              ),
              'borders' => array(
                  'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                  ),
              ),
              'fill' => array(
                  'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                  'rotation' => 90,
                  'startcolor' => array(
                    'argb' => 'FFA0A0A0',
                  ),
                  'endcolor' => array(
                    'argb' => 'FFFFFFFF',
                  ),
              ),
            );
     
            $objPHPExcel->getActiveSheet()->mergeCells('A1:' . $char . '1');
            $objPHPExcel->getActiveSheet()->setCellValue('A1', $file_name);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
     
            $styleArray = array(
              'font' => array(
                'bold' => true,
              ),
              'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              ),
              'borders' => array(
                  'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                  ),
              ),
              'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'rotation' => 90,
                'startcolor' => array(
                    'argb' => 'FFA0A0A0',
                ),
                'endcolor' => array(
                    'argb' => 'FFFFFFFF',
                ),
              ),
            );
            $objPHPExcel->getActiveSheet()->getStyle('A'. $header_row .':' . $char . $header_row)->applyFromArray($styleArray);
            foreach($header_cols as $key => $el) {
                 $floor = floor(($key)/26);
                 $reminder = ($key) % 26;
                 $char = ($floor > 0 ? chr(ord("A") + $floor-1) : '').chr(ord("A") + $reminder);
                 $objPHPExcel->getActiveSheet()->getColumnDimension($char)->setAutoSize(true);
            }
            $styleArray = array(
              'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY,
              ),
              'borders' => array(
                'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN,
                  'color' => array('argb' => 'FFFF0000'),
                ),
              ),
            );
            $objPHPExcel->getActiveSheet()->getStyle('A'. $start_row .':' . $char . $end_row)->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->setTitle($sheetTitle);
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file_name . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        ob_end_flush();
        exit; 
    }
}
