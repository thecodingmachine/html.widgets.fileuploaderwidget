<?php
namespace Mouf\Html\Widgets\FileUploaderWidget;

class JsFileUploader {
    private $allowedExtensions = array();
    private $sizeLimit = 10485760;
    private $file;
    private $fileSave;
    private $uploadDirectory;

    function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760){        
        $allowedExtensions = array_map("strtolower", $allowedExtensions);
            
        $this->allowedExtensions = $allowedExtensions;        
        $this->sizeLimit = $sizeLimit;
        
        $this->checkServerSettings();       

        if (isset($_GET['qqfile'])) {
            $this->file = new JsUploadedFileXhr();
        } elseif (isset($_FILES['qqfile'])) {
            $this->file = new JsUploadedFileForm();
        } else {
            $this->file = false; 
        }
    }
    
    public function getFile() {
    	return $this->file;
    }
    
    public function getFileName() {
    	return $this->file->getName();
    }
    
    private function checkServerSettings(){        
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));        
        
        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';             
            die("{'error':'increase post_max_size and upload_max_filesize to $size'}");    
        }        
    }
    
    private function toBytes($str){
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        $val = (int) $val;
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;        
        }
        return $val;
    }
    
    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory, $fileName = null, $replaceOldFile = false){
        if (!is_writable($uploadDirectory)){
            return array('error' => "Server error. Upload directory isn't writable.");
        }

        $this->uploadDirectory = $uploadDirectory;
        
        if (!$this->file){
            return array('error' => 'No files were uploaded.');
        }
        
        $size = $this->file->getSize();
        
        if ($size == 0) {
            return array('error' => 'File is empty');
        }
        
        if ($this->sizeLimit && $size > $this->sizeLimit) {
            return array('error' => 'File is too large');
        }
        
        if($fileName === null)
        	$fileName = $this->file->getName();
        $pathinfo = pathinfo($fileName);
        $filename = $pathinfo['filename'];
        //$filename = md5(uniqid());
        
        $pathinfo = pathinfo($this->file->getName());
        $ext = $pathinfo['extension'];

        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
        }
        
        if(!$replaceOldFile){
            /// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                $filename .= '_'.date('YmdHis').rand(1, 100000);
            }
        }
        
        $this->fileSave = $filename.'.'.$ext;
        if ($this->file->save($uploadDirectory . $filename . '.' . $ext)){
            return array('success'=>true);
        } else {
            return array('error'=> 'Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered');
        }
        
    }    
    
    function getFileSave($withPath = false) {
    	if($withPath)
    		return $this->uploadDirectory.$this->fileSave;
    	return $this->fileSave;
    }
}
