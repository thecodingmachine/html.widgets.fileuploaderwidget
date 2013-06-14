FileUploader installation guide
===============================

File Uploader combines HTML, JS and PHP code to upload file. It chose the best solution, HTML5 or ajax, of client browser to send file.
Moreover, it is possible to send many file at the same time and drag and drop it.

Dependencies
------------

FileUploader comes as a *Composer* package and requires the "Mouf" framework to run.
The first step is therefore to [install Mouf](http://www.mouf-php.com/).

Once Mouf is installed, you can process to the FileUploader installation.

Install FileUploader
--------------------

Edit your *composer.json* file, and add a dependency on *mouf/html.widgets.fileuploaderwidget*.

A minimal *composer.json* file might look like this:
```
	{
	    "require": {
	        "mouf/mouf": "~2.0",
	        "mouf/html.widgets.fileuploaderwidget": "2.0.*@dev"
	    },
	    "autoload": {
	        "psr-0": {
	            "Test": "src/"
	        }
	    },
	    "minimum-stability": "dev"
	}
```
As explained above, FileUploader is a package of the Mouf framework. Mouf allows you (amoung other things) to visualy "build" your project's dependencies and instances.

To install the dependency, run
	php composer.phar install

This *composer.json* file assumes that you will put your code in the "src" directory, and that you will use the "Test" namespace and respect the PSR-0 naming scheme.
Be sure to create those directories (src/Test) before running the install process.
If you do not understand what "namespace" or "PSR-0" means, *stop right now*, and head over the [autoloading section of Composer](http://getcomposer.org/doc/01-basic-usage.md#autoloading) and the [PSR-0 documentation](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md).
	
At this point, the FileUploader packages is installed. You should be created instance to implement it. Start the Mouf admin interface at http://localhost/{yourproject}/vendor/mouf/mouf



