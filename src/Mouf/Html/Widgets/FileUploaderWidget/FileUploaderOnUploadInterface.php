<?php

/**
 * Classes implementing the UploadifyOnUploadInterface interface can be notified when a file is uploaded through a UploadifySingleFileWidget.
 * These classes must be registered in the UploadifySingleFileWidget::listeners property.
 * 
 * @author David Negrier
 */
interface FileUploaderOnUploadInterface {
	
	/**
	 * This method is called by an FileUploadWidget before the file copy.
	 * 
	 * <p>Please note 2 parameters are passed in reference. It is a PHP array containing additional data
	 * to be passed back to the page. The PHP array will be converted to JSON and be sent to the page.
	 * You can put additional parameters in this array, and read those parameters in your page, using the
	 * onComplete property that will trigger some Javascript function when the upload
	 * is complete, client-side.</p>
	 * 
	 * <p>The $result array will always contain one of two key:</p>
	 * <pre>$result = array("success"=>"true")</pre>
	 * <pre>$result = array("error"=>"[message]")</pre>
	 * 
	 * @param string $targetFile The final path of the uploaded file. When the beforeUpload method is called, the file is not yet there. In this function, you can change the value of $targetFile since it is passed by reference
	 * @param string $fileName The final name of the uploaded file. When the beforeUpload method is called, the file is not yet there. In this function, you can change the value of $fileName since it is passed by reference
	 * @param string $fileId The fileId that was set in the uploadify widget (see FileUploadWidget::fileId)
	 * @param FileUploaderWidget $widget
	 * @param array $result The result array that will be returned to the page as a JSON object.
	 * @param array $parameters Parameters add in the Javascript call.
	 * @return boolean Return false to cancel the upload
	 */
	public function beforeUpload(&$targetFile, &$fileName, $fileId, $instance, array &$result, array $parameters);
	
	/**
	 * This method is called by an FileUploadWidget after the file copy.
	 * 
	 * <p>Please note 2 parameters are passed in reference. It is a PHP array containing additional data
	 * to be passed back to the page. The PHP array will be converted to JSON and be sent to the page.
	 * You can put additional parameters in this array, and read those parameters in your page, using the
	 * onComplete property that will trigger some Javascript function when the upload
	 * is complete, client-side.</p>
	 * 
	 * <p>The $result array will always contain one of two key:</p>
	 * <pre>$result = array("success"=>"true")</pre>
	 * <pre>$result = array("error"=>"[message]")</pre>
	 * 
	 * @param string $targetFile The final path of the uploaded file. When the afterUpload method is called, the file is there.
	 * @param string $fileId The fileId that was set in the uploadify widget (see FileUploadWidget::fileId)
	 * @param FileUploaderWidget $widget
	 * @param array $result The result array that will be returned to the page as a JSON object.
	 * @param array $parameters Parameters add in the Javascript call.
	 * @return boolean Return false to cancel the upload
	 */
	public function afterUpload($targetFile, $fileId, $instance, array &$result, array $parameters);
}