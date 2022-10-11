<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Inv\Repositories\Models\Payment;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Lms\Transactions;
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

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->eodDate = now()->toDateString();
        $this->eodDate = '2022-10-10';
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // $this->checkDuplicatePaymentRecords();
        // $this->checkDuplicateDisbursalRecords();        
        $this->checkEODTallyRecords();        
    }

    private function checkDuplicatePaymentRecords()
    {
        $dupPayments = Payment::select(DB::raw('*, count(*) AS paymentCount'))
                            ->whereDate('created_at', $this->eodDate)
                            ->where('trans_type', config('lms.TRANS_TYPE.REPAYMENT'))
                            ->where('action_type', 1)
                            ->groupBy(['user_id', 'amount', 'utr_no', 'unr_no', 'cheque_no'])
                            ->havingRaw('paymentCount > 1')
                            ->get();
        dd($dupPayments, 'duplicate_payment_alert');
    }

    private function checkDuplicateDisbursalRecords()
    {
        $dupDisbursals = Disbursal::select(DB::raw('*, count(*) AS disbCount'))
                            ->whereDate('created_at', $this->eodDate)
                            ->groupBy(['user_id', 'disburse_amount', 'disbursal_batch_id'])
                            ->havingRaw('disbCount > 1')
                            ->get();
        dd($dupDisbursals);
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
            $this->tallyMisMatchReportGenerateAndSendWithEmail($data, $reportName = 'tally_mismatch_eod_'.$this->eodDate);
        }
    }

    private function tallyMisMatchReportGenerateAndSendWithEmail($exceldata, $reportName)
    {
        $rows  = 5;
        $sheet =  new PHPExcel();
        $sheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$rows, 'Customer Name')
            ->setCellValue('B'.$rows, 'Transaction #')
            ->setCellValue('C'.$rows, 'Voucher Date')
            ->setCellValue('D'.$rows, 'Transaction Date')
            ->setCellValue('E'.$rows, 'Transaction Type')
            ->setCellValue('F'.$rows, 'Invoice No')
            ->setCellValue('G'.$rows, 'Invoice Date')
            ->setCellValue('H'.$rows, 'Amount')
            ->setCellValue('I'.$rows, 'Entry Type');
        $sheet->getActiveSheet()->getStyle('A'.$rows.':R'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
        $rows++;
        foreach($exceldata as $rowData){
            $sheet->setActiveSheetIndex(0)
            ->setCellValueExplicit('A'.$rows, $rowData['cust_name'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('B'.$rows, $rowData['loan_ac'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('C'.$rows, $rowData['virtual_ac'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('D'.$rows, Carbon::parse($rowData['trans_date'])->format('d-m-Y'), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('E'.$rows, $rowData['trans_no'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('F'.$rows, $rowData['invoice_no'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('G'.$rows, Carbon::parse($rowData['invoice_date'])->format('d-m-Y'), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('H'.$rows, number_format($rowData['invoice_amt'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('I'.$rows, number_format($rowData['margin_amt'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('J'.$rows, number_format($rowData['disb_amt'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('K'.$rows, number_format($rowData['out_amt'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('L'.$rows, $rowData['out_days'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('M'.$rows, $rowData['tenor'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('N'.$rows, Carbon::parse($rowData['due_date'])->format('d-m-Y'), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('O'.$rows, number_format($rowData['due_amt'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('P'.$rows, $rowData['od_days'], \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('Q'.$rows, number_format($rowData['od_amt'],2), \PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('R'.$rows, $rowData['remark'], \PHPExcel_Cell_DataType::TYPE_STRING);
            $rows++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');

        $dirPath = 'public/report/temp/maturityReport/'.date('Ymd');
        if (!Storage::exists($dirPath)) {
            Storage::makeDirectory($dirPath);
        }

        $storage_path = storage_path('app/'.$dirPath);
        // $filePath = $storage_path.'/Maturity Report'.time().'_'.rand(1111, 9999).'_'.'.xlsx';
        $filePath = $storage_path.$reportName.'.xlsx';
        $objWriter->save($filePath);
        return $filePath;
    }
}
