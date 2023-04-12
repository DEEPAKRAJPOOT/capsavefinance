<?php

namespace App\Helpers;

use Auth;
use Illuminate\Support\Facades\Storage;
use PDF as DPDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Inv\Repositories\Models\UserFile;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;

class FileHelper {

    protected $diskStoragePath;
    protected $appRepo;

    public function __construct(InvAppRepoInterface $app_repo) {
       $this->diskStoragePath = Storage::disk('public');
       $this->appRepo = $app_repo;
    }

    public function getLatestFileName($appId, $fileType='banking', $extType='json'){
      $scanpath = $this->getToUploadPath($appId, $fileType);
      if (is_dir($scanpath) == false) {
        $files = [];
      }else{
        $files = scandir($scanpath, SCANDIR_SORT_DESCENDING);
      }
      $files = array_diff($files, [".", ".."]);
      natsort($files);
      $files = array_reverse($files, false);
      $filename = "";
      if (!empty($files)) {
        foreach ($files as $key => $file) {
          $fileparts = pathinfo($file);
          $filename = $fileparts['filename'];
          $ext = $fileparts['extension'];
          if ($extType == $ext) {
             break;
          }
        }
      }
                
      $included_no = preg_replace('#[^0-9]+#', '', $filename);
      $file_no = substr($included_no, strlen($appId));
      if (empty($file_no) && empty($filename)) {
        $new_file = $appId.'_'.$fileType.".$extType";
        $curr_file = '';
      }else{
        $file_no = (int)$file_no + 1;
        $curr_file = $filename.".$extType";
        $new_file = $appId.'_'.$fileType.'_'.$file_no . ".$extType";
      }
      $fileArr = array(
        'curr_file' => $curr_file,
        'new_file' => $new_file,
      );
      return $fileArr;
    }

    public function getToUploadPath($appId, $type = 'banking'){
      $storageDiskPath = $this->diskStoragePath;
      $touploadpath = $storageDiskPath->path('user/docs/'.$appId);
      if(!$storageDiskPath->exists('user/docs/' .$appId)) {
          $storageDiskPath->makeDirectory('user/docs/' .$appId.'/banking', 0777, true);
          $storageDiskPath->makeDirectory('user/docs/' .$appId.'/finance', 0777, true);
          $touploadpath = $storageDiskPath->path('user/docs/' .$appId);
      }
      return $touploadpath .= ($type == 'banking' ? '/banking' : '/finance');
    }

    public function uploadFileWithContent($active_filename_fullpath, $fileContents) {
        $defaultPath = $this->diskStoragePath->path('');
        $realPath = str_replace($defaultPath, '', $active_filename_fullpath);
        $isSaved = $this->diskStoragePath->put($realPath, $fileContents);
        if ($isSaved) {
            $mimetype = $this->diskStoragePath->mimeType($realPath);
            $size = $this->diskStoragePath->size($realPath);
            $inputArr['file_path'] = $realPath;
            $inputArr['file_type'] = $mimetype;
            $inputArr['file_name'] = basename($realPath);
            $inputArr['file_size'] = $size;
            $inputArr['file_encp_key'] =  md5('2');
            $inputArr['created_by'] = 1;
            $inputArr['updated_by'] = 1;
            return $inputArr;
        }
        return $isSaved;
    }

    public function readFileContent($active_filename_fullpath) {
        $defaultPath = $this->diskStoragePath->path('');
        $realPath = str_replace($defaultPath, '', $active_filename_fullpath);
        $fileContent = $this->diskStoragePath->get($realPath);
        return $fileContent;
    }

    public function array_to_excel($toExportData, $file_name = "", $moreDetails = [], $path = null, $isFileSave = false) {
        $moreDetails = array_chunk(array_filter($moreDetails), 2, true);
        $requiredRowsForDetails = ceil(count($moreDetails) / 2);
        ob_start();
        if(empty($file_name)) {
            $file_name = "Report - " . _getRand(15).".xlsx";
        }
        $activeSheet = 0;
        $lastkey = array_key_last($toExportData);
        $objSpreadsheet = new Spreadsheet();

        $ExtraRow = 0;
          $styleArr2 = $styleArray = array(
              'font' => array(
                'bold' => true,
              ),
              'alignment' => array(
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
              ),
              'borders' => array(
                  'top' => array(
                    'style' => Border::BORDER_THIN,
                  ),
              ),
              'fill' => array(
                  'rotation' => 90,
                  'startColor' => array(
                    'argb' => 'FFA0A0A0',
                  ),
                  'endColor' => array(
                    'argb' => 'FFFFFFFF',
                  ),
              ),
            );
        foreach ($moreDetails as $kk => $vv) {
          $objSpreadsheet->setActiveSheetIndex($activeSheet);
          $coll = 0;
          foreach ($vv as $moreIndex => $moreValue) {
              $chrr = chr(ord('A') + $coll);
              $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coll, $ExtraRow, $moreIndex);
              $objSpreadsheet->getActiveSheet()->getStyle($chrr. $ExtraRow)->applyFromArray($styleArray);
              unset($styleArr2['font']);
              $coll++;
              $chrr = chr(ord('A') + $coll);
              $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coll, $ExtraRow, $moreValue);
              $objSpreadsheet->getActiveSheet()->getStyle($chrr. $ExtraRow)->applyFromArray($styleArr2);
              $coll = $coll+2;
          }
          $ExtraRow++;
        }
        foreach ($toExportData as $title => $data) {
            if ($title != $lastkey) {
                $objSpreadsheet->createSheet();
            }
            if (empty($data) || !isset($data[0])) {
              $data[0] = [
                '' => 'No Records Found for the exported report.',
              ];
            }
            $rec_count = count($data[0]);
            $header_cols = array_keys($data[0]);
            $sheetTitle = $title;
            $objSpreadsheet->setActiveSheetIndex($activeSheet);
            $activeSheet++;
            $column = 0;
            $header_row = $ExtraRow + 1;
            $start_row = $ExtraRow + 2;
            $row = $start_row;
            $column = 0;
            $floor = floor($rec_count/26);
            $reminder = $rec_count % 26;
            $char = ($floor > 0 ? chr(ord("A") + $floor-1) : '').chr(ord("A") + $reminder);
            
            //Fact Payment File Name sub string
            $factDateFormatFlag = 0;
            switch (strtolower(substr(trim($file_name),0,12))) {
              case 'fact-payment':
                $factDateFormatFlag = 1;
                break;
              case 'fact-journal':
                $factDateFormatFlag = 2;
                break;
            }

            foreach($data as $key => $item) {
              foreach($item as $key1 => $item1) {
                if($factDateFormatFlag == 1){
                  $objSpreadsheet->getActiveSheet()
                  ->setCellValueByColumnAndRow($column, $row, $item1)  
                  ->getStyle('C'.$row)
                  ->getNumberFormat()
                  ->setFormatCode('dd-mm-yyyy');
                  $objSpreadsheet->getActiveSheet()
                  ->setCellValueByColumnAndRow($column, $row, $item1)  
                  ->getStyle('F'.$row)
                  ->getNumberFormat()
                  ->setFormatCode('dd-mm-yyyy');
                }elseif($factDateFormatFlag == 2){
                  $objSpreadsheet->getActiveSheet()
                  ->setCellValueByColumnAndRow($column, $row, $item1)  
                  ->getStyle('B'.$row)
                  ->getNumberFormat()
                  ->setFormatCode('dd-mm-yyyy');
                }else{
                  $objSpreadsheet->getActiveSheet()->setCellValueByColumnAndRow($column, $row, $item1);
                }
                
                $column++;
              }
              $argb = "FFFFFFFF";
              if ($row % 2 == 1){
                $argb = "FFE0E0E0";
              }
              $styleArray = array(
                'fill' => array(
                  'fillType' => Fill::FILL_SOLID,
                  'startColor' => array(
                    'argb' => $argb,
                  ),
                ),
              );
              $objSpreadsheet->getActiveSheet()->getStyle('A'. $row .':' . $char . $row)->applyFromArray($styleArray);
              $column = 0;
              $row++;
            }
            $end_row = $row - 1;
            $row = $header_row;
            $column = 0;
            foreach($header_cols as $key) {
               $key = ucwords(str_replace('_', ' ', $key));
               $objSpreadsheet->getActiveSheet()->getCellByColumnAndRow($column, $row)->setValueExplicit($key, DataType::TYPE_STRING);
                  $column++;
            }
            $styleArray = array(
              'font' => array(
                'bold' => true,
              ),
              'alignment' => array(
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
              ),
              'borders' => array(
                  'top' => array(
                    'style' => Border::BORDER_THIN,
                  ),
              ),
              'fill' => array(
                  'fillType' => Fill::FILL_GRADIENT_LINEAR,
                  'rotation' => 90,
                  'startColor' => array(
                    'argb' => 'FFA0A0A0',
                  ),
                  'endColor' => array(
                    'argb' => 'FFFFFFFF',
                  ),
              ),
            );

            $styleArray = array(
              'font' => array(
                'bold' => true,
              ),
              'alignment' => array(
                'horizontal' => Alignment::HORIZONTAL_CENTER,
              ),
              'borders' => array(
                  'top' => array(
                    'style' => Border::BORDER_THIN,
                  ),
              ),
              'fill' => array(
                'fillType' => Fill::FILL_SOLID,
                'rotation' => 90,
                'startColor' => array(
                    'argb' => 'FFA0A0A0',
                ),
                'endColor' => array(
                    'argb' => 'FFFFFFFF',
                ),
              ),
            );
            $objSpreadsheet->getActiveSheet()->getStyle('A'. $header_row .':' . $char . $header_row)->applyFromArray($styleArray);
            foreach($header_cols as $key => $el) {
                 $floor = floor(($key)/26);
                 $reminder = ($key) % 26;
                 $char = ($floor > 0 ? chr(ord("A") + $floor-1) : '').chr(ord("A") + $reminder);
                 $objSpreadsheet->getActiveSheet()->getColumnDimension($char)->setAutoSize(true);
            }
            $styleArray = array(
              'alignment' => array(
                'horizontal' => Alignment::HORIZONTAL_JUSTIFY,
              ),
              'borders' => array(
                'allborders' => array(
                  'style' => Border::BORDER_THIN,
                  // 'color' => array('argb' => 'FFFF0000'),
                ),
              ),
            );
            $objSpreadsheet->getActiveSheet()->getStyle('A'. $start_row .':' . $char . $end_row)->applyFromArray($styleArray);
            $objSpreadsheet->getActiveSheet()->setTitle($sheetTitle);
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file_name . '"');
        header('Cache-Control: max-age=0');
        if ($isFileSave == true) {
           if (!Storage::exists('/public/nach/request')) {
                Storage::makeDirectory('/public/nach/request');
            }
            $filePath = '';
            $fileData = [];
            $storage_path = storage_path('app/public/nach/request');
            $filePath = $storage_path.'/'.$file_name;
            $objWriter = IOFactory::createWriter($objSpreadsheet, 'Excel2007');
            $objWriter->save($filePath); 
            $fileContent = $this->readFileContent($filePath);
            $fileData = $this->uploadFileWithContent($filePath, $fileContent);
            $file = UserFile::create($fileData);
            $batchId = _getRand(12);
            $nachBatchData['req_file_id'] = $file->file_id;
            $nachBatchData['batch_id'] = $batchId;
            $this->appRepo->saveNachBatch($nachBatchData, null);
        }
        $objWriter = IOFactory::createWriter($objSpreadsheet, 'Excel2007');
        $objWriter->save('php://output');
        ob_end_flush();
        exit; 
    } 

    public function array_to_pdf($pdfArr, $view='reports.commonReport') {
      // return view($view, $pdfArr);
       DPDF::setOptions(['isHtml5ParserEnabled'=> true]);
       $pdf = DPDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'defaultPaperSize' => 'a4'])
                ->loadView($view, $pdfArr,[],'UTF-8');
        return $pdf;
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

    public function excelNcsv_to_array($inputFileName = '', $header = []) {
      $respArray = [
        'status' => 'success',
        'message' => 'success',
        'data' => [],
      ];
      try{
          $inputFileType  =   IOFactory::identify($inputFileName);
          $objReader      =   IOFactory::createReader($inputFileType);
          $objSpreadsheet    =   $objReader->load($inputFileName);
          $sheet = $objSpreadsheet->getActiveSheet();
          // $sheet = $sheet->removeColumnByIndex(15);
          $highestRow = $sheet->getHighestRow(); 
          $highestColumn = $sheet->getHighestDataColumn();
          for ($row = 1; $row <= $highestRow; $row++){ 
              $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,  NULL,  TRUE, FALSE);
              $rowRec = $rowData[0];
              if ($row == 1) {
                $header = (!empty($header) ? $header : $rowRec);
                continue;
              }
              $record = array_combine($header, $rowRec);
              $respArray['data'][] = $record;
          }
      }catch(\Exception $e){
          $respArray['data'] = [];
          $respArray['status'] = 'fail';
          $respArray['message'] = str_replace($inputFileName, '', $e->getMessage());
      }
      return $respArray;
    }

public function exportCsv($data=[],$columns=[],$fileName='',$extraDataArray=[])
{
  $respArray = [
    'status' => 'success',
    'message' => 'success',
  ];
  try {  
        $headers = array(
            "Content-type"        => "application/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );
        $callback = function() use($data, $columns, $extraDataArray, $fileName) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Token ID='.$extraDataArray['TOKEN_ID']]);
            fputcsv($file, [$extraDataArray['NOTE']]);
            fputcsv($file, []);
            fputcsv($file, $columns);
            foreach ($data as $key=>$data) {
                fputcsv($file, $data);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
      }catch(\Exception $e){
        $respArray['status'] = 'fail';
        $respArray['message'] = str_replace($fileName, '', $e->getMessage());
        return $respArray;
     }
    }

    public function csvToArray($filename = '', $delimiter = ',')
    {
        $respArray = [
            'status' => 'success',
            'message' => 'success',
            'data' => [],
          ];
      try{
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        if (($handle = fopen($filename, 'r')) !== false)
        {
            $rows=1;
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
              $num = count($row);
              if ($rows != 1 && $rows != 2 && $rows != 3){
                if (!$header){
                    $header = $row;
                }else{
                    $respArray['data'][] = array_combine($header, $row);
                }
              }
              $rows++;
              for ($c=0; $c < $num; $c++) {
                $regex  = "/Token ID=/";
                // preg_match returns true or false.
                if(preg_match($regex, $row[$c], $match)) 
                {
                  $respArray['TOKEN_ID']  = str_replace("Token ID=","",$row[$c]);
                }
              }
            }
            fclose($handle);
        }
    }catch(\Exception $e){
        $respArray['data'] = [];
        $respArray['status'] = 'fail';
        $respArray['message'] = str_replace($filename, '', $e->getMessage());
    }
    return $respArray;
    }

    public static function uploadUnSettledTransCsv($data=[],$columns=[],$fileName='',$extraDataArray=[],$paymentId=null,$type='upload')
    {
      $respArray = [
        'status' => 'success',
        'message' => 'success',
        'data' => [],
      ];
      try{
        $inputArr = [];
        if ($data) {
          if($type == 'download'){
              if (!Storage::exists('/public/payment/' . $paymentId.'/download')) {
                Storage::makeDirectory('/public/payment/' . $paymentId.'/download', 0777, true);
              }
              $destinationPath = storage_path('app').'/public/payment/' . $paymentId.'/download/'.$fileName;
              $fp = fopen($destinationPath, 'w+');
              fputcsv($fp, ['Token ID='.$extraDataArray['TOKEN_ID']]);
              fputcsv($fp, [$extraDataArray['NOTE']]);
              fputcsv($fp, []);
              fputcsv($fp, $columns);
              foreach ($data as $key=>$data) {
                  fputcsv($fp, $data);
              }
              fclose($fp);
              $dbpath = 'payment/' . $paymentId.'/download/'.$fileName;
              $inputArr['file_path'] = $dbpath;
              $fileInfo =  pathinfo($destinationPath);
              $inputArr['file_type'] = 'text/'.$fileInfo['extension'];//\File::mimeType($destinationPath);
              $inputArr['file_name'] = $fileName;
              $inputArr['file_size'] = \File::size($destinationPath);
              $inputArr['file_encp_key'] =  md5('2');
        }else{
          if ($data['upload_unsettled_trans']) {
          if (!Storage::exists('/public/payment/' . $paymentId.'/upload')) {
            Storage::makeDirectory('/public/payment/' . $paymentId.'/upload', 0777, true);
          }
          $destinationPath = '/payment/' . $paymentId.'/upload';
          $fileName = $data['upload_unsettled_trans']->getClientOriginalName();
          //$path = Storage::put($destinationPath,$data['upload_unsettled_trans'],'file.csv');
          $path = Storage::disk('public')->putFileAs($destinationPath, $data['upload_unsettled_trans'],$fileName);
          $inputArr['file_path'] = $path;
        }
        $inputArr['file_type'] = $data['upload_unsettled_trans']->getClientMimeType();
        $inputArr['file_name'] = $data['upload_unsettled_trans']->getClientOriginalName();
        $inputArr['file_size'] = $data['upload_unsettled_trans']->getSize();
        $inputArr['file_encp_key'] =  md5('2');
        }
        $inputArr['created_by'] = 1;
        $inputArr['updated_by'] = 1;
        $respArray = [
          'status' => 'success',
          'message' => 'success',
          'data' => $inputArr,
        ];
       }
      }catch(\Exception $e){
        $respArray['data'] = [];
        $respArray['status'] = 'fail';
        $respArray['message'] = str_replace($fileName, '', $e->getMessage());
      }
      return $respArray;
    }

    public static function validationMessage($errorMessageID=0){
      $errorMessage = [
        0 => 'Some Error while performing task. Please try again!',
        1 => 'Some Error while downloading file. Please try again!',
        2 => 'Some Errors while downloading file. Please try again!',
        3 => 'Some Error while saving file. Please try again!',
        4 => 'Some Error while downloading file. Please try again!',
        5 => 'Please fill the data continuously till 6th column in the sheet',
        6 => 'File does not contain any record.',
        7 => 'File does not contain Token ID.',
        8 => 'Token Data does not contain payload.',
        9 => 'Token Data does not matched. Please try again!',
        10 => 'Please fill the correct details.',
        11 => 'Please fill at least one Payment value.',
        12 => 'Some Error while saving file. Please try again!',
        13 => 'Payment value must be numeric.',
        14 => 'Some Errors while uploading a file. Please try again!',
        15 => 'Upload File does not match with latest downloaded file. Please upload latest file!',
        16 => 'File record does not found. Please download latest file to upload!',
        17 => 'Some Errors while uploading a file. Please try again!',
        18 => 'Some Token Data missing in Token ID. Please try again!',
        19 => 'Trans ID does not exists in our system. Please try again!',
        20 => 'Trans ID does not exists in our system. Please try again!',
      ];
      return $errorMessage[$errorMessageID];
    }
}
