<?php
	require_once dirname(dirname(__FILE__))."/src/pbalan/CarouselBuilder/CarouselBuilder.php";
	require_once '../vendor/autoload.php';
	require_once '../autoload.php';
	use pbalan\FileUploader;
	use pbalan\DirectoryParser;
	
	$dest = $_SERVER['DOCUMENT_ROOT'].'/carousel-builder/upload';
	
	if(true===isset($_GET['carouselName']) && false===empty($_GET['carouselName']) && true===is_string($_GET['carouselName']))
	{
		$carouselName = $_GET['carouselName'];
	} else {
		$carouselName = 'carousel';
	}
	// create active directory to differentiate images which are currently required to show on carousel
	$activeDir = 'active';
	
	// uniform directory separator
	$dest = str_replace('\\','/',$dest);
	
	$carouselObj = new pbalan\CarouselBuilder\CarouselBuilder($carouselName, $dest);
	if(true===$carouselObj->getError())
	{
		echo "carousel with name $carouselName exists! Please provide a different name.";
		exit;
	}
	$dest = $carouselObj->getCarouselDir();
	
	$dirObj = new pbalan\DirectoryParser\DirectoryParser($dest);
	$dirObj->addRelativeDirectory($activeDir);
	$return = $dirObj->getFileList($dest);
	
	if(true===empty($return)){
		echo "Your carousel is empty. Please specify images to display as carousel"; exit;
	} else {
		
	}