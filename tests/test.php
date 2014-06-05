<?php
	require_once dirname(dirname(__FILE__))."/src/pbalan/CarouselBuilder/CarouselBuilder.php";
	require_once '../vendor/autoload.php';
	require_once '../autoload.php';
	use pbalan\FileUploader;
	use pbalan\DirectoryParser;
	
	$dest = dirname(__FILE__).'/upload';
	
	$dirObj = new pbalan\DirectoryParser\DirectoryParser();
	$dirObj->createDirectory($dest);
	$return = $dirObj->getFileList($dest);
	
	var_dump($return);