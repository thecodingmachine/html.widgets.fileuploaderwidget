<?php
namespace Mouf\Html\Widgets\FileUploaderWidget;

/**
 * This class represent an HTML/Flash file upload widget enabling the upload of a single file.
 *
 * @Component
 */
use Mouf\MoufManager;

use Mouf\Html\HtmlElement\HtmlElementInterface;
use Mouf\Utils\Value\ValueUtils;
use Mouf\Utils\Value\ValueInterface;

class FileUploaderWidget implements HtmlElementInterface {
		
	/**
	 * Number of fields displayed
	 *
	 * @var int
	 */
	protected static $count = 0;
	
	/**
	 * The list of file extensions for the files to upload, separated by a ",".
	 * <p>For instance: "jpg","gif","png"</p>
	 * 
	 * @Property
	 * @var string
	 */
	public $fileExtensions;
	
	/**
	 * Each file size limit in bytes.
	 * This option isn't supported in all browsers.
	 * 
	 * @Property
	 * @var string
	 */
	public $sizeLimit;
	
	/**
	 * Each file min size limit in bytes.
	 * This option isn't supported in all browsers.
	 * 
	 * @Property
	 * @var string
	 */
	public $minSizeLimit;
	
	/**
	 * Debug mode.
	 * Set to true to output server response to console
	 * 
	 * @Property
	 * @var boolean
	 */
	public $debug = false;
	
	/**
	 * The destination directory for the file to be written. 
	 * If it does not start with "/", this is relative to ROOT_PATH.
	 * The directory is created if it does not exist.
	 * 
	 * You can of course set this value dynamically, in your code, using
	 * <pre>$instance->directory = "my/directory";</pre>
	 * 
	 * @Property
	 * @var string
	 */
	public $directory;
	
	/**
	 * The destination file name for the file to be written.
	 * This is a unique file name and cannot contain "/".
	 *
	 * Most of the time, you will set this value dynamically, in your code, using
	 * <pre>$instance->fileName = "myFileName.ext";</pre>
	 * 
	 * If not set, the name of the file provided by the user is used instead.
	 * 
	 * @Property
	 * @var string
	 */
	public $fileName;
	
	/**
	 * If you want to trigger some code when the file is uploaded, you will need to give the file a unique ID.
	 * You should set this ID programmatically, using:
	 * <pre>$instance->fileId = $myId;</pre>
	 * Then, you should register a listener that will be triggered when the file is uploaded (see the "listeners"
	 * property). The ID will be passed to the listener when an upload is completed.
	 * 
	 * @Property
	 * @var string
	 */
	public $fileId;
	
	/**
	 * Enable or disabled the multiple file upload.
	 * Authorize the user to send many file in 1 time (by drag & drop or explorer).
	 * Note: It's only a graphic parameter.
	 * 
	 * @Property
	 * @var boolean
	 */
	public $multiple = true;
	
	/**
	 * Replace the other file if they has the same folder and name.
	 * If disabled the file is renamed with _ and random number.
	 * By default replace is true
	 * 
	 * @Property
	 * @var boolean
	 */
	public $replace = true;
	
	/**
	 * A list of instances that will be notified when an upload occurs.
	 * To be registered, an instance should implement the FileUploaderOnUploadInterface interface.
	 * 
	 * @Property
	 * @var array<FileUploaderOnUploadInterface>
	 */
	public $listenersBefore;
	
	/**
	 * A list of instances that will be notified when an upload occurs.
	 * To be registered, an instance should implement the FileUploaderOnUploadInterface interface.
	 * 
	 * @Property
	 * @var array<FileUploaderOnUploadInterface>
	 */
	public $listenersAfter;
	
	/**
	 * The name of the javascript function to trigger on upload completed.
	 * Function called : function(id, fileName, responseJSON){} 
	 * 
	 * @Property
	 * @var string
	 */
	public $onComplete;
	
	/**
	 * The name of the javascript function to trigger on upload progress.
	 * Function called : function(id, fileName, loaded, total){} 
	 * 
	 * @Property
	 * @var string
	 */
	public $onProgress;
	
	/**
	 * The name of the javascript function to trigger on submit.
	 * Function called : function(id, fileName){} 
	 * 
	 * @Property
	 * @var string
	 */
	public $onSubmit;
	
	/**
	 * The name of the javascript function to trigger on cancel.
	 * Function called : function(id, fileName){} 
	 * 
	 * @Property
	 * @var string
	 */
	public $onCancel;
	
	/**
	 * The name of the javascript function to trigger on send message.
	 * Function called : function(message){ alert(message); }
	 * 
	 * @Property
	 * @var string
	 */
	public $showMessage;
	

	/**
	 * Text display by default on the file upload buttom
	 *
	 * @Property
	 * @var string|ValueInterface
	 */
	public $textDefault;

	/**
	 * Text display on hover of the file upload buttom
	 *
	 * @Property
	 * @var string|ValueInterface
	 */
	public $textHover;

	/**
	 * Text display to cancel the upload
	 *
	 * @Property
	 * @var string|ValueInterface
	 */
	public $textCancel;

	/**
	 * Text display when the file failed
	 *
	 * @Property
	 * @var string|ValueInterface
	 */
	public $textFailed;
	
	/**
	 * If you want to add parameter to your application
	 * The parameters must be serializable, to be saved in SESSION
	 * 
	 * @var mixed
	 */
	private $params;
	
	/**
	 * Renders the object in HTML.
	 * The Html is echoed directly into the output.
	 *
	 */
	public function toHtml() {
		echo $this->returnHtmlString();
	}
	
	/**
	 * @var array<string, string>
	 */
	public $inlineStyles = array("float" =>"left", "margin-right" => "20px");
	
	/**
	 * @var bool
	 */
	public $useInlineStyles = true;
	
	
	/**
	 * Return an HTML string to render the object
	 *
	 */
	public function returnHtmlString() {
		self::$count++;
		$id = "mouf_fileupload_".self::$count;
	
		//Parse InlineStyles
		$styles = "";
		if ($this->useInlineStyles){
			foreach ($this->inlineStyles as $attribute => $value){
				$styles .= $attribute.": ".$value."; ";
			}
		}
	
		$html = '<div id="'.plainstring_to_htmlprotected($id).'" style="'.$styles.'">
		<noscript><p>Please enable JavaScript to use file uploader.</p></noscript>
		</div>';
	
		$version = basename(dirname(__FILE__));
	
		$uniqueId = date('YmdHis').rand(0, 9999999);
		$moufManager = MoufManager::getMoufManager();
		$moufManager->findInstanceName($this);
		$thisInstanceName = $moufManager->findInstanceName($this);
	
		$scriptDataArray = array("uniqueId"=>$uniqueId,
				"fileId" =>$this->fileId,
				"instanceName" =>$thisInstanceName);
		if($this->fileName)
			$scriptDataArray['fileName']= $this->fileName;
			
		$fileUploaderParam = array();
		if($this->fileExtensions) {
			if(strpos($this->fileExtensions, '"') === false) {
				$exts = explode(',', $this->fileExtensions);
				$extensions = '';
				foreach ($exts as $ext) {
					if($extensions != '') {
						$extensions .= ',';
					}
					$extensions .= '"'.$ext.'"';
				}
				$fileUploaderParam['allowedExtensions'] = '['.$extensions.']';
			}
			else {
				$fileUploaderParam['allowedExtensions'] = '['.$this->fileExtensions.']';
			}
		}
		if($this->multiple)
			$fileUploaderParam['multiple'] = 'true';
		else
			$fileUploaderParam['multiple'] = 'false';
		if($this->sizeLimit) {
			$fileUploaderParam['sizeLimit'] = $this->sizeLimit;
		}
		if($this->minSizeLimit) {
			$fileUploaderParam['minSizeLimit'] = $this->minSizeLimit;
		}
		if($this->multiple)
			$fileUploaderParam['debug'] = 'true';
		else
			$fileUploaderParam['debug'] = 'false';
		if($this->onCancel) {
			$fileUploaderParam['onCancel'] = $this->onCancel;
		}
		if($this->onComplete) {
			$fileUploaderParam['onComplete'] = $this->onComplete;
		}
		if($this->onProgress) {
			$fileUploaderParam['onProgress'] = $this->onProgress;
		}
		if($this->onSubmit) {
			$fileUploaderParam['onSubmit'] = $this->onSubmit;
		}
		if($this->showMessage) {
			$fileUploaderParam['showMessage'] = $this->showMessage;
		}
		$html .= '<script type="text/javascript">
		var uploader'.self::$count.' = new qq.FileUploader({
		element: document.getElementById("'.plainstring_to_htmlprotected($id).'"),
		action: "'.ROOT_URL.'vendor/mouf/html.widgets.fileuploaderwidget/src/direct/upload.php",';
		if($this->textDefault) {
			$html .= 'textDefault: "'.ValueUtils::val($this->textDefault).'",';
		}
		if($this->textHover) {
			$html .= 'textHover: "'.ValueUtils::val($this->textHover).'",';
		}
		if($this->textCancel) {
			$html .= 'textCancel: "'.ValueUtils::val($this->textCancel).'",';
		}
		if($this->textFailed) {
			$html .= 'textFailed: "'.ValueUtils::val($this->textFailed).'",';
		}
		$html .= 'params: '.json_encode($scriptDataArray);
		foreach ($fileUploaderParam as $key => $value) {
			$html .= ','.$key.':'.$value;
		}
			
		$html .= '});
		</script>';
	
		// Start a session using the session manager.
		$moufManager = MoufManager::getMoufManager();
		$moufManager->getInstance('sessionManager')->start();
	
		$_SESSION["mouf_fileupload_autorizeduploads"][$uniqueId] = array("path"=>$this->getFileUploadPath(),
				"fileId"=>$this->fileId,
				"instanceName"=>$thisInstanceName);
		if($this->params === null)
			$this->params = array();
		$_SESSION["mouf_fileupload_autorizeduploads"][$uniqueId]['params'] = serialize($this->params);
		return $html;
	}
	
	/**
	 * Save parameter. They must be serializable.
	 * 
	 * @param mixed $params
	 */
	public function setParams($params) {
		$this->params = $params;
	}
	
	/**
	 * Add parameter. They must be serializable.
	 * Caution, elements are saved in array
	 * 
	 * @param mixed $params
	 */
	public function addParams($params) {
		if($this->params === null)
			$this->params = $params;
		else
			$this->params = array_merge($this->params, $params);
	}

	/**
	 * Return the parameters saved in instance.
	 * @return mixed
	 */
	public function getParams($uniqueId) {
		// Start a session using the session manager.
		$moufManager = MoufManager::getMoufManager();
		$moufManager->getInstance('sessionManager')->start();
		if(isset($_SESSION["mouf_fileupload_autorizeduploads"][$uniqueId]['params']))
			return unserialize($_SESSION["mouf_fileupload_autorizeduploads"][$uniqueId]['params']);
		else
			return array();
	}
	
	/**
	 * Returns the complete absolute path to the file that will be uploaded.
	 * @return string
	 */
	public function getFileUploadPath() {
		$directory = $this->directory;
		if (strpos($directory, '/') !== 0 && strpos($directory, ':') !== 1) {
			$directory = ROOT_PATH.$directory;
		}
		rtrim($directory, DIRECTORY_SEPARATOR);
		$directory .= DIRECTORY_SEPARATOR;
		$file = $directory.basename($this->fileName);
		$file = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $file);
		return $file;
	}
	
	/**
	 * Call all listener before upload file.
	 *
	 * @param string $targetFile The final path of the uploaded file. When the afterUpload method is called, the file is there.
	 *  * @param string $fileName The final name of the uploaded file. When the beforeUpload method is called, the file is not yet there. In this function, you can change the value of $fileName since it is passed by reference
	 * @param string $fileId The fileId that was set in the uploadify widget (see FileUploadWidget::fileId)
	 * @param array $result The result array that will be returned to the page as a JSON object.
	 * @param string $uniqueId Unique id of file uploader form.
	 */
	public function triggerBeforeUpload(&$targetFile, &$fileName, $fileId, array &$returnArray, $uniqueId) {
		if (is_array($this->listenersBefore)) {
			foreach ($this->listenersBefore as $listener) {
				/* @var $listener UploadifyOnUploadInterface */
				$result = $listener->beforeUpload($targetFile, $fileName, $fileId, $this, $returnArray, $this->getParams($uniqueId));
				if($result === false) {
					return false;
				}
				if(is_array($result)) {
					$returnArray = array_merge($returnArray, $result);
				}
			}
		}
		return true;
	}
	
	/**
	 * Call all listener after upload file.
	 *
	 * @param string $targetFile The final path of the uploaded file. When the afterUpload method is called, the file is there.
	 * @param string $fileId The fileId that was set in the uploadify widget (see FileUploadWidget::fileId)
	 * @param array $result The result array that will be returned to the page as a JSON object.
	 * @param string $uniqueId Unique id of file uploader form.
	 */
	public function triggerAfterUpload(&$targetFile, $fileId, array &$returnArray, $uniqueId) {
		if (is_array($this->listenersAfter)) {
			foreach ($this->listenersAfter as $listener) {
				/* @var $listener UploadifyOnUploadInterface */
				$result = $listener->afterUpload($targetFile, $fileId, $this, $returnArray, $this->getParams($uniqueId));
				if($result === false) {
					$returnArray = array_merge($returnArray, $result);
					break;
				}
			}
		}
	}
}
?>