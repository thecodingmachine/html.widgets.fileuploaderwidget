<?php

require_once dirname(__FILE__)."/../../../../../../Mouf.php";

Mouf::getSessionManager()->start();

$uniqueId = $_REQUEST['uniqueId'];

$sessArray = array("path"=>$_REQUEST['path'],
					"fileId"=>$_REQUEST['fileId'],
					"instanceName"=>$_REQUEST['instanceName']);

$targetFile = $sessArray["path"];
$fileName = '';
if(isset($_REQUEST['fileName'])) {
	$fileName = $_REQUEST['fileName'];
}
if(!$fileName)
	$fileName = null;
if (empty($sessArray['instanceName'])) {
	$returnArray['error'] = 'No instance name';
	echo json_encode($returnArray);
	exit;
}
$instance = MoufManager::getMoufManager()->getInstance($sessArray['instanceName']);
		
if(!is_array($_SESSION["mouf_fileupload_autorizeduploads"][$uniqueId])){
	$returnArray['error'] = 'session error';
	echo json_encode($returnArray);
	exit;
}
$diff = array_diff($sessArray, $_SESSION["mouf_fileupload_autorizeduploads"][$uniqueId]);
if(count($diff)){
	$returnArray['error'] = 'session not match';
	echo json_encode($returnArray);
	exit;
}

$targetPath = dirname($targetFile);

$returnArray = array('success'=>'true');

// Initialize the update
$allowedExtensions = json_decode($instance->fileExtensions);
if(!$allowedExtensions) {
	$allowedExtensions = array();
}
// max file size in bytes
$sizeLimit = $instance->sizeLimit;

// Object to retrieve file send by user
$uploader = new JsFileUploader($allowedExtensions, $sizeLimit);

// If the user cannot add fileName
if(!$fileName) {
	// Retrieve fileName in the instance or the fileName send by user
	if($instance->fileName)
		$fileName = $instance->fileName;
	else
		$fileName = $uploader->getFileName();
}

/* @var $instance FileUploaderWidget */
// Call listener Before
$instance->triggerBeforeUpload($targetFile, $fileName, $sessArray["fileId"], $returnArray, $uniqueId);

if (!is_dir($targetFile)) {
	mkdir(str_replace('//','/', $targetFile), 0755, true);
}
if (!isset($returnArray['error'])) {
	
	$returnUpload = $uploader->handleUpload($targetFile, $fileName, $instance->replace);
	$targetFile = $uploader->getFileSave(true);
	if (!$returnUpload) {
		$returnArray['error'] = 'no return after JSFileUpload';
	}
}

// Call listener After
$instance->triggerAfterUpload($targetFile, $sessArray["fileId"], $returnArray, $uniqueId);

echo htmlspecialchars(json_encode($returnArray), ENT_NOQUOTES);
