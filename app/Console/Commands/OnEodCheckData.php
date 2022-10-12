<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Inv\Repositories\Models\Payment;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Master\EmailTemplate;
use Illuminate\Support\Facades\Storage;
use PHPExcel_IOFactory;
use Carbon\Carbon;
use PHPExcel;
use DB;

class OnEodCheckData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eod:check-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To check eod data for mismatch records';

    protected $eodDate = '';
    protected $emailTo = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->eodDate = now()->toDateString();
        // $this->eodDate = '2022-10-10';
        $this->emailTo = 'pankaj.sharma@zuron.in';
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->checkDuplicatePaymentRecords();
        $this->checkDuplicateDisbursalRecords();        
        $this->checkEODTallyRecords();        
    }

    private function checkDuplicatePaymentRecords()
    {
        $dupPayments = Payment::withTrashed()->select(DB::raw('*, CONCAT_WS("", utr_no, unr_no, cheque_no) AS com_utr_no, count(*) AS paymentCount'))
                            ->whereDate('created_at', $this->eodDate)
                            ->where('trans_type', config('lms.TRANS_TYPE.REPAYMENT'))
                            ->where('action_type', 1)
                            ->groupBy(['user_id', 'amount', 'utr_no', 'unr_no', 'cheque_no'])
                            ->havingRaw('paymentCount > 1')
                            ->get();

        if (count($dupPayments)) {
            $filePath = $this->paymentDuplicateByCustomerReportGenerate($dupPayments, $reportName = '/duplicate-payments');
            $emailTemplate  = EmailTemplate::getEmailTemplate("REPORT_DUPLICATE_PAYMENTS");
            if ($emailTemplate) {
                $emailData            = \Helpers::getDailyReportsEmailData($emailTemplate);
                $emailData['to']      = $this->emailTo;
                $emailData['attachment'] = $filePath;
                \Event::dispatch("NOTIFY_DUPLICATE_PAYMENTS", serialize($emailData));
            }
        }
    }

    private function checkDuplicateDisbursalRecords()
    {
        $dupDisbursals = Disbursal::select(DB::raw('*, count(*) AS disbCount'))
                            ->whereDate('created_at', $this->eodDate)
                            ->groupBy(['user_id', 'disburse_amount', 'disbursal_batch_id'])
                            ->havingRaw('disbCount > 1')
                            ->get();

        if (count($dupDisbursals)) {
            $filePath = $this->disbursalDuplicateByBatchReportGenerate($dupDisbursals, $reportName = '/duplicate-disbursals');
            $emailTemplate  = EmailTemplate::getEmailTemplate("REPORT_DUPLICATE_DISBURSALS");
            if ($emailTemplate) {
                $emailData            = \Helpers::getDailyReportsEmailData($emailTemplate);
                $emailData['to']      = $this->emailTo;
                $emailData['attachment'] = $filePath;
                \Event::dispatch("NOTIFY_DUPLICATE_DISBURSALS", serialize($emailData));
            }
        }        
    }

    private function checkEODTallyRecords()
    {
        $where = [/*['is_posted_in_tally', '=', '0'],*/ ['created_at', '>=', "$this->eodDate 00:00:00"],['created_at', '<=', "$this->eodDate 23:59:59"]];
        $journalArray = Transactions::getJournalTxnTally($where)->toArray();
        $disbursalArray = Transactions::getDisbursalTxnTally($where)->toArray();
        $refundArray = Transactions::getRefundTxnTally($where)->toArray();

        $tally_data = array_merge($disbursalArray, $journalArray, $refundArray);
        $tally_trans_ids = array_column($tally_data, 'trans_id');
        $transactions = Transactions::whereIn('trans_id', $tally_trans_ids)
                                ->doesntHave('tallyEntry')
                                ->get();

        if (count($transactions)) {
            $filePath = $this->tallyMisMatchReportGenerate($transactions, $reportName = '/tally');
            $emailTemplate  = EmailTemplate::getEmailTemplate("REPORT_TALLY_MISMATCH");
            if ($emailTemplate) {
                $emailData            = \Helpers::getDailyReportsEmailData($emailTemplate);
                $emailData['to']      = $this->emailTo;
                $emailData['attachment'] = $filePath;
                \Event::dispatch("NOTIFY_TALLY_MISMATCH", serialize($emailData));
            }
        }
    }

    private function tallyMisMatchReportGenerate($exceldata, $reportName)
    {
        $rows  = 5;
        $sheet =  new PHPExcel();
        $sheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$rows, 'Customer #')
            ->setCellValue('B'.$rows, 'Transaction #')
            ->setCellValue('C'.$rows, 'Transaction Date')
            ->setCellValue('D'.$rows, 'Transaction Type')
            ->setCellValue('E'.$rows, 'Amount')
            ->setCellValue('F'.$rows, 'Entry Type');
        $sheet->getActiveSheet()->getStyle('A'.$rows.':F'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
        $rows++;
        foreach($exceldata as $rowData){
            $sheet->setActiveSheetIndex(0)
            ->setCellValueExplicit('A'.$rows, $rowData->lmsUser->customer_id, \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('B'.$rows, $rowData->trans_id, \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('C'.$rows, Carbon::parse($rowData->trans_date)->format('d-m-Y'), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('D'.$rows, $rowData->transType->trans_name, \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('E'.$rows, number_format($rowData->amount, 2), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('F'.$rows, $rowData->entry_type == 1 ? 'Credit' : 'Debit', \PHPExcel_Cell_DataType::TYPE_STRING);
            $rows++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');

        $dirPath = 'public/report/temp/eodMisMatchChecks/'.date('Ymd');
        if (!Storage::exists($dirPath)) {
            Storage::makeDirectory($dirPath);
        }

        $storage_path = storage_path('app/'.$dirPath);
        $filePath = $storage_path.$reportName.'.xlsx';
        $objWriter->save($filePath);
        return $filePath;
    }

    private function paymentDuplicateByCustomerReportGenerate($exceldata, $reportName)
    {
        $rows  = 5;
        $sheet =  new PHPExcel();
        $sheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$rows, 'Customer #')
            ->setCellValue('B'.$rows, 'UTR No')
            ->setCellValue('C'.$rows, 'Action Type')
            ->setCellValue('D'.$rows, 'Transaction Type')
            ->setCellValue('E'.$rows, 'Amount');
        $sheet->getActiveSheet()->getStyle('A'.$rows.':E'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
        $rows++;
        foreach($exceldata as $rowData){
            $sheet->setActiveSheetIndex(0)
            ->setCellValueExplicit('A'.$rows, $rowData->lmsUser->customer_id, \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('B'.$rows, $rowData->com_utr_no, \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('C'.$rows, $rowData->action_type == 1 ? 'Receipt' : '', \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('D'.$rows, $rowData->transType->trans_name, \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('E'.$rows, number_format($rowData->amount, 2), \PHPExcel_Cell_DataType::TYPE_STRING);
            $rows++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');

        $dirPath = 'public/report/temp/eodMisMatchChecks/'.date('Ymd');
        if (!Storage::exists($dirPath)) {
            Storage::makeDirectory($dirPath);
        }

        $storage_path = storage_path('app/'.$dirPath);
        $filePath = $storage_path.$reportName.'.xlsx';
        $objWriter->save($filePath);
        return $filePath;
    }

    private function disbursalDuplicateByBatchReportGenerate($exceldata, $reportName)
    {
        $rows  = 5;
        $sheet =  new PHPExcel();
        $sheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$rows, 'Customer #')
            ->setCellValue('B'.$rows, 'Disburse Type')
            ->setCellValue('C'.$rows, 'Batch #')
            ->setCellValue('D'.$rows, 'Transaction #')
            ->setCellValue('E'.$rows, 'Amount');
        $sheet->getActiveSheet()->getStyle('A'.$rows.':E'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
        $rows++;
        foreach($exceldata as $rowData){
            $sheet->setActiveSheetIndex(0)
            ->setCellValueExplicit('A'.$rows, $rowData->lms_user->customer_id, \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('B'.$rows, $rowData->disburse_type == 1 ? 'Online' : 'Mannual/Offline', \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('C'.$rows, $rowData->disbursal_batch->batch_id, \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('D'.$rows, $rowData->tran_id, \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('E'.$rows, number_format($rowData->disburse_amount, 2), \PHPExcel_Cell_DataType::TYPE_STRING);
            $rows++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');

        $dirPath = 'public/report/temp/eodMisMatchChecks/'.date('Ymd');
        if (!Storage::exists($dirPath)) {
            Storage::makeDirectory($dirPath);
        }

        $storage_path = storage_path('app/'.$dirPath);
        $filePath = $storage_path.$reportName.'.xlsx';
        $objWriter->save($filePath);
        return $filePath;
    }
}
