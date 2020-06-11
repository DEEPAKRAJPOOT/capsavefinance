<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

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
   
}
