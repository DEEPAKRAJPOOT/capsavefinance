<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Inv\Repositories\Models\Master\EmailTemplate;
use App\Inv\Repositories\Contracts\ReportInterface;
use Illuminate\Support\Facades\Storage;
use PHPExcel_IOFactory;
use Carbon\Carbon;
use PHPExcel;
use Helpers;

class UtilizationReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $needConsolidatedReport;
    private $emailTo;
    private $anchor;
    private $sendMail;
    private $reportsRepo;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($needConsolidatedReport, $emailTo, $anchor = null)
    {
        $this->needConsolidatedReport = $needConsolidatedReport;
        $this->emailTo                = $emailTo;
        $this->anchor                 = $anchor;
        $this->sendMail               = false;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ReportInterface $reportsRepo)
    {
        $this->reportsRepo = $reportsRepo;

        if ($this->needConsolidatedReport) {
            ini_set("memory_limit", "-1");
            $this->generateConsolidatedReport();
        }

        if (is_array($this->anchor) && isset($this->anchor['anchor_id'])) {
            $this->generateAnchorReport($this->anchor['anchor_id']);
        }
    }

    private function generateConsolidatedReport()
    {
        $data = $this->reportsRepo->getUtilizationReport([], $this->sendMail);
        if ($this->sendMail) {
            $this->reportGenerateAndSendWithEmail($data);
        }
    }

    private function generateAnchorReport($anchorId)
    {
        $this->sendMail = false;
        $data           = $this->reportsRepo->getUtilizationReport(['anchor_id' => $anchorId], $this->sendMail);

        if ($this->sendMail) {
            $this->reportGenerateAndSendWithEmail($data);
        }
    }

    private function reportGenerateAndSendWithEmail($data)
    {
        $emailTemplate  = EmailTemplate::getEmailTemplate("REPORT_UTILIZATION");
        if ($emailTemplate) {
            $compName                = is_array($this->anchor) && isset($this->anchor['comp_name']) ? $this->anchor['comp_name'] : '';
            $emailData               = Helpers::getDailyReportsEmailData($emailTemplate, $compName);
            $filePath                = $this->downloadUtilizationExcel($data);
            $emailData['email']      = $this->emailTo;
            $emailData['attachment'] = $filePath;
            \Event::dispatch("NOTIFY_UTILIZATION_REPORT", serialize($emailData));
        }
    }

    private function downloadUtilizationExcel($exceldata) 
    {
        $rows = 5;

        $sheet =  new PHPExcel();
        foreach($exceldata as $rowData){
            $sheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$rows, 'Anchor Name')
            ->setCellValue('B'.$rows, 'Program Name')
            ->setCellValue('C'.$rows, 'Sub Program Name')
            ->setCellValue('D'.$rows, '# of Clients sanctioned')
            ->setCellValue('E'.$rows, '# of Overdue Customers')
            ->setCellValue('F'.$rows, 'Total Over Due Amount');
            $sheet->getActiveSheet()->getStyle('A'.$rows.':F'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
            $rows++;

            $sheet->setActiveSheetIndex(0)
            ->setCellValue('A' . $rows, $rowData['anchor_name'])
            ->setCellValue('B' . $rows, $rowData['prgm_name'])
            ->setCellValue('C' . $rows, $rowData['sub_prgm_name'])
            ->setCellValue('D' . $rows, $rowData['client_sanction'])
            ->setCellValue('E' . $rows, $rowData['ttl_od_customer'])
            ->setCellValue('F' . $rows, number_format($rowData['ttl_od_amt'],2)); 
            $rows++;
            $rows++;
            if(!empty($rowData['disbursement'])){
                foreach($rowData['disbursement'] as $disb){
                    $rows++;
                    $sheet->setActiveSheetIndex(0)
                    ->setCellValue('A'.$rows, 'Client Name')
                    ->setCellValue('B'.$rows, 'Customer ID')
                    ->setCellValue('C'.$rows, 'Virtual Account #')
                    ->setCellValue('D'.$rows, 'Client Sanction Limit')
                    ->setCellValue('E'.$rows, 'Limit Utilized Limit')
                    ->setCellValue('F'.$rows, 'Available Limit')
                    ->setCellValue('G'.$rows, 'Expiry Date')
                    ->setCellValue('H'.$rows, 'Sales Person Name')
                    ->setCellValue('I'.$rows, 'Sub Program Name')
					->setCellValue('J'.$rows, 'Anchor Name');
                    $sheet->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
                    $rows++;
                    $sheet->setActiveSheetIndex(0)
                    ->setCellValue('A'.$rows, $disb['client_name'])
                    ->setCellValue('B'.$rows, $disb['user_id'])
                    ->setCellValue('C'.$rows, $disb['virtual_ac'])
                    ->setCellValue('D'.$rows, number_format($disb['client_sanction_limit'],2))
                    ->setCellValue('E'.$rows, number_format($disb['limit_utilize'],2))
                    ->setCellValue('F'.$rows, number_format($disb['limit_available'],2))
                    ->setCellValue('G'.$rows, Carbon::parse($disb['end_date'])->format('d/m/Y') ?? NULL)
                    ->setCellValue('H'.$rows, $disb['sales_person_name'])
                    ->setCellValue('I'.$rows, $disb['sub_prgm_name'])
					->setCellValue('J'.$rows, $rowData['anchor_name']);
                    $rows++;
                    $rows++;
                    if(!empty($disb['invoice'])){
                        $sheet->setActiveSheetIndex(0)
                        ->setCellValue('B'.$rows,'Invoice #')
                        ->setCellValue('C'.$rows,'Invoice Date')
                        ->setCellValue('D'.$rows,'Invoice Amount')
						->setCellValue('E'.$rows,'Invoice Approved')
                        ->setCellValue('F'.$rows,'Margin Amount')
                        ->setCellValue('G'.$rows,'Amount Disbursed')
                        ->setCellValue('H'.$rows,'Principal OverDue Days')
						->setCellValue('I'.$rows,'Principal OverDue Amount')
						->setCellValue('J'.$rows,'Over Due Days')
                        ->setCellValue('K'.$rows,'Over Due Interest Amount');

                        $sheet->getActiveSheet()->getStyle('A'.$rows.':K'.$rows)->applyFromArray(['font' => ['bold'  => true]]);
                        $rows++;

                        foreach($disb['invoice'] as $inv){
                            $sheet->setActiveSheetIndex(0)
                            ->setCellValue('B'.$rows,$inv['invoice_no'])
                            ->setCellValue('C'.$rows,Carbon::parse($inv['invoice_date'])->format('d/m/Y') ?? NULL)
                            ->setCellValue('D'.$rows,number_format($inv['invoice_amt'],2))
							->setCellValue('E'.$rows,number_format($inv['approve_amt'],2))
                            ->setCellValue('F'.$rows,number_format($inv['margin_amt'],2))
                            ->setCellValue('G'.$rows,number_format($inv['disb_amt'],2))
							->setCellValue('H'.$rows,$inv['principal_od_days'])
							->setCellValue('I'.$rows,number_format($inv['principal_od_amount'],2))
                            ->setCellValue('J'.$rows,$inv['od_days'])
                            ->setCellValue('K'.$rows,number_format($inv['od_amt'],2));
                            $rows++;
                        }
                    }
                }
            }
            $rows++;
        }
        
        $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');
        
        $dirPath = 'public/report/temp/utilizationReport/'.date('Ymd');
        if (!Storage::exists($dirPath)) {
            Storage::makeDirectory($dirPath);
        }
        $storage_path = storage_path('app/'.$dirPath);
        $filePath = $storage_path.'/Utilization Report'.time().'_'.rand(1111, 9999).'_'.'.xlsx';
        $objWriter->save($filePath);
        return $filePath;
    }
}
