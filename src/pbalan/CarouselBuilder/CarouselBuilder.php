<?php
	/*	CarouselBuilder is a class that provides HTML embedded Carousel Widget
	 *	@Author: Prashant Balan
	 **/
	
	namespace pbalan\CarouselBuilder;
	use pbalan\FileUploader;
	use pbalan\DirectoryParser;
	
	class CarouselBuilder{
		private $dir = null;
		private $recurse = false;
		private $relative = null;
		private $previewDir = '/asset/preview';
		private $allowedExtn = array('jpg','jpeg','gif','png');
		private $activeDir = null;
		private $inActiveDir = null;
		private $overlayText = null;
		private $overlayStyle = null;
		private $image = null;
		private $carouselName = null;
		private $error = false;
		
		public function __construct($carouselName, $dir)
		{
			$this->carouselName = $carouselName;
			$this->dir = $dir;
			$this->checkDirectoryFlow();
			$this->dir .= $this->carouselName;
			if(false===is_dir($this->dir)){
				$this->error = false;
			} else {
				$this->error = true;
			}
		}
		public function getCarouselDir(){
			return $this->dir;
		}
		public function getError(){
			return $this->error;
		}
		public function checkDirectoryFlow()
		{
			if(substr($this->dir, (strlen($this->dir)-2))!='/' || substr($this->dir, (strlen($this->dir)-2))!='\\')
			{
				$this->dir .= '/';
			}
			return true;
		}
		
		public function previewMode()
		{
			$dirObj = new pbalan\DirectoryParser\DirectoryParser();
			$dirObj->copyToDirectory($this->previewDir, $this->activeDir);
		}
		
		public function displayCarousel()
		{
			$html = '';
			
			return $html;
		}
		
		public function overlaySettings($image, $overlayText='', $overlayStyle='')
		{
			if(false===empty($overlayText))
			{
				$this->overlayText = $overlayText;
			}
			if(false===empty($overlayStyle))
			{
				$this->overlayStyle = $overlayStyle;
			}
			
		}
		
		public function createConfig($image='')
		{
			if(false===empty($image))
			{
				$this->image = $image;
			}
		}
		
		public function SetUpCarousel($carouselName, $dir='', $allowedExtn=array())
		{
			if(false===empty($dir) && true===is_dir($dir))
			{
				$this->dir = $dir;
			}
			if(false===empty($allowedExtn) && true===is_array($allowedExtn))
			{
				$this->allowedExtn = $allowedExtn;
			}
			else if(false===empty($allowedExtn) && false===is_array($this->allowedExtn))
			{
				echo "Invalid file extension. An Array expected. Ex: <br/> array('jpg','jpeg','gif','png')";
				exit;
			}
			if(null===$this->dir || false===is_dir($this->dir))
			{
				echo 'Invalid directory path..';
				exit;
			}
			
			$dirObj = new pbalan\DirectoryParser\DirectoryParser();
			$fileList = $dirObj->getFileList($this->dir, $this->allowedExtn, true);
			
			if(true===empty($fileList))
			{
				echo 'No images in the directory. Upload using the Admin Panel.'; exit;
			}
			else
			{
				$html = '';
				if(true===is_array($fileList))
				{
					foreach($fileList as $file){
						$status = false;
						if(false!==strpos(strtolower($file), '/active/'))
						{
							$status = true;
						}
						$src = str_replace($_SERVER['DOCUMENT_ROOT'],IMAGE_URL, $file);
						$html .= '<div class="wrapCells">
									<div class="picture">
										<img src="'.$src.'" />
									</div>
									<div class="checkBx">
										<input type="checkbox" name="fileChange[]" value="'.$file.'"'.(true===$status ? ' checked="true"':'').' />
									</div>
								  </div>';
					}
				}
				else
				{
					echo 'An unexpected error occurred, please try later or contact <a href="mailto:'.ADMIN_MAIL.'> administrator.</a>';
					exit;
				}
				return $html;
			}
		}
	}
?>