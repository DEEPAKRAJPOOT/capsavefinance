<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use PDF as DPDF;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;
use PHPExcel_Cell_DataType;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;

class FileHelper {

    protected $diskStoragePath;

    public function __construct() {
       $this->diskStoragePath = Storage::disk('public');
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
            $mimetype = $this->diskStoragePath->getMimeType($realPath);
            $metadata = $this->diskStoragePath->getMetaData($realPath);
            $inputArr['file_path'] = $realPath;
            $inputArr['file_type'] = $mimetype;
            $inputArr['file_name'] = basename($realPath);
            $inputArr['file_size'] = $metadata['size'];
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

    public function array_to_excel($toExportData, $file_name = "") {
        ob_start();
        if(empty($file_name)) {
            $file_name = "Report - " . _getRand(15).".xlsx";
        }
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
                  // 'color' => array('argb' => 'FFFF0000'),
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

    public function array_to_pdf($pdfArr) {
       DPDF::setOptions(['isHtml5ParserEnabled'=> true]);
       $pdf = DPDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'defaultPaperSize' => 'a4'])
                ->loadView('reports.commonReport', ['pdfArr' => $pdfArr],[],'UTF-8');
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
   
}
