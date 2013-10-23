How to use Simple File Uploader
===============================

This object provides a simple use of File Upload with few lines of code.
When you upload a file, it is saved in temp folder. After the form save you could retrieve all file send and move it in your folder

Step 1: create instance
-----------------------

Create an instance of Simple File Uploader. In our example is named : simpleFileUpload
![File Upload create instance](https://raw.github.com/thecodingmachine/html.widgets.fileuploaderwidget/2.0/doc/images/create_instance.png)
![File Upload create instance found](https://raw.github.com/thecodingmachine/html.widgets.fileuploaderwidget/2.0/doc/images/create_instance_found.png)
![File Upload create instance name](https://raw.github.com/thecodingmachine/html.widgets.fileuploaderwidget/2.0/doc/images/create_instance_name.png)

Next, set the parameters :
 - input name: It's like the name of your input element in a form;
 - directory: to store your file;
 - session: sessionManagerInterface to start the session automatically.


Others parameters are mandatory. The most important:
 - OnlyOneFile: If you want to send only 1 file (many by default). This delete the file contain in the destination folder to store only one. Note: it's not a graphic parameter;
 - noTemporary : You can activate it to don't send the file in the temporary folder. The file is saved in the destination folder;
 - fileExtensions: Limit the extension name of file send;
 - sizeLimit: The max size of file in byte;
 - minSizeLimit: The min size of file in byte;
 - fileName: To rename the file with a name;
 - multiple: authorize the user to send many file in 1 time (by drag & drop or explorer). Note: It's only a graphic parameter.

![File Upload instance](https://raw.github.com/thecodingmachine/html.widgets.fileuploaderwidget/2.0/doc/images/instance.png)


Add in your controller
----------------------
Add your instance in your controller
```
	/**
	 *
	 * @var SimpleFileUploaderWidget
	 */
	public $fileUpload;
```

In Mouf add to your controller the fileUpload instance
![File Upload instance to controller](https://raw.github.com/thecodingmachine/html.widgets.fileuploaderwidget/2.0/doc/images/controller.png)

Add in your form
------------------------
Add the code in your form to display the button :

```
<form action="your_method" method="post">
<?php
	$this->fileUpload->toHtml();
?>
<input type="submit" name="submit" value="Send" />
</form>
```
![File Upload form](https://raw.github.com/thecodingmachine/html.widgets.fileuploaderwidget/2.0/doc/images/form.png)

Retrieve your files
---------------------------
In the save action, you could retrieve the file. The parameter is the name of hidden input passed in inputName (above).

Move all file in your folder:
```
	$this->fileupload->moveFile();
```
If you want to change the destination folder (example: to add an id)
```
	$this->fileupload->moveFile(null, 'my_new_folder');
```

There is a function to get the list of files in temporary folder:
```
	$this->fileupload->hasFileToMove();
```

In listener afterUpload
-----------------------
Your controller (class) must implements FileUploaderOnUploadInterface. you will add 2 functions beforeUpload and afterUpload.
In afterUpload you could move your file with the function moveFileWithoutSave. The first parameter is ALWAYS $result['targetFolder'].
```
	$this->fileUpload->moveFileWithoutSave();
```