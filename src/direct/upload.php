<?php
use Mouf\Html\Widgets\FileUploaderWidget\JsFileUploader;
use Mouf\MoufManager;

require_once '../../../../../mouf/Mouf.php';

if (!defined('ROOT_URL') && function_exists('apache_getenv')) {
	define('ROOT_URL', apache_getenv("BASE")."/../../../../../");
}

$moufManager = MoufManager::getMoufManager();
$moufManager->getInstance('sessionManager')->start();

$uniqueId = $_REQUEST['uniqueId'];

$sessArray = array("path"=>$_REQUEST['path'],
		"fileId"=>$_REQUEST['fileId'],
		"instanceName"=>$_REQUEST['instanceName']);

foreach ($sessArray as $key => $value) {
	if($value == 'null')
		$sessArray[$key] = null;
}

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
$instance = $moufManager->getInstance($sessArray['instanceName']);

if(!is_array($_SESSION["mouf_fileupload_autorizeduploads"][$uniqueId])){
	$returnArray['error'] = 'session error';
	echo json_encode($returnArray);
	exit;
}

// Check if there is difference between form and session
foreach ($sessArray as $key => $value) {
	if((string)$_SESSION["mouf_fileupload_autorizeduploads"][$uniqueId][$key] != $value) {
		// 		echo $key.' - '.$value.'!='.$_SESSION["mouf_fileupload_autorizeduploads"][$uniqueId][$key];
		$returnArray['error'] = 'session not match';
		echo json_encode($returnArray);
		exit;
	}
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

$continue = $instance->triggerBeforeUpload($targetFile, $fileName, $sessArray["fileId"], $returnArray, $uniqueId);
if($continue === false) {
	$returnArray['success'] = null;
	$returnArray['info'] = 'Cancel by Trigger before upload';
	echo htmlspecialchars(json_encode($returnArray), ENT_NOQUOTES);
	exit();
}

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
