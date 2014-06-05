<?php
	
	namespace pbalan\CarouselBuilder;	
	use pbalan\CarouselBuilder;
	
	class CarouselBuilderTest extends CarouselBuilder
	{
		
		protected $allowedExtn = array();
		protected $dir = null;
		protected $recurse = false;
		
		public function testDirectoryParse()
		{
			$this->dir = parent::createDirectory($_SERVER['DOCUMENT_ROOT'].'/tests/upload',0777,true);
			return $this->testRead($this->dir);
		}
	}
	$newObj = new DirectoryParserTest();
	$contentDir = $newObj->testRead();
	var_dump($contentDir);
?>