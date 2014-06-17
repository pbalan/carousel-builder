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
		
		public function __construct($carouselName='', $dir='')
		{
            if(false==empty($carouselName))
			{
                $this->carouselName = $carouselName;
            }
            if(false===empty($dir) && true===is_dir($dir))
            {
                $this->dir = $dir;
                $this->checkDirectoryFlow();
                $this->dir .= $this->carouselName;
                if(false===is_dir($this->dir)){
                    $this->error = false;
                } else {
                    $this->error = true;
                }
            }
		}
		public function getCarouselDir(){
			return $this->dir;
		}
		public function getError(){
			return $this->error;
		}
		
        public function checkDirectoryFlow($dir='')
		{
            if(false===empty($dir))
            {
                $dir = str_replace('\\','/',$dir);
                if(substr($dir, (strlen($dir)-1))!='/')
                {
                    $dir .= '/';
                }
                return $dir;
            } else {
                $this->dir = str_replace('\\','/',$this->dir);
                if(substr($this->dir, (strlen($this->dir)-1))!='/')
                {
                    $this->dir .= '/';
                }
                return true;
            }
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
		
		public function createConfig($dir='', $allowedExtn='', $content='')
		{
			if(false===empty($dir))
			{
				$this->dir = $dir;
			}
            if(false===empty($allowedExtn))
			{
				$this->allowedExtn = $allowedExtn;
			}
            $this->checkDirectoryFlow();
            $dirObj = new DirectoryParser\DirectoryParser();
            $contentArr = $this->getDataForConfig($this->dir,$this->allowedExtn);
            $content = json_encode($contentArr);
            $listCarousel = $dirObj->createFile("carousel-store.json", $this->dir, $content);
            
            return $listCarousel;
		}
		
        public function getDataForConfig($dir='', $allowedExtn='')
        {
            if(false===empty($dir))
			{
				$this->dir = $dir;
			}
            if(false===empty($allowedExtn))
			{
				$this->allowedExtn = $allowedExtn;
			}
            $configAll = array();
            if(false===empty($dir) && true===is_dir($dir))
            {
                $dirObj = new DirectoryParser\DirectoryParser();
                $dirList = $dirObj->listDirectories($dir);
                if(false===empty($dirList) && true===is_array($dirList))
                {
                    foreach($dirList as $d)
                    {
                        $width = 0;
                        $height = 0;
                        $carouselName = explode('_',basename($d));
                        if(true===isset($carouselName[1]) && false===empty($carouselName[1]))
                        {
                            $dimensionArr = explode('x',$carouselName[1]);
                        } else {
                            $dimensionArr = array();
                        }
                        if(true===isset($carouselName[0])){
                            $carouselName = $carouselName[0];
                        } else {
                            $carouselName = $carouselName;
                        }
                        if(true===isset($dimensionArr[0]) && false===empty($dimensionArr[0]))
                        {
                            $width = $dimensionArr[0];
                        }
                        if(true===isset($dimensionArr[1]) && false===empty($dimensionArr[1]))
                        {
                            $height = $dimensionArr[1];
                        }
                        $config = array();
                        $config['name'] = $carouselName;
                        $config['path'] = $d;
                        $config['carouselWidth'] = $width;
                        $config['carouselHeight'] = $height;
                        $config['active'] = $dirObj->getFileList($d.'/active',$this->allowedExtn,false);
                        $config['inactive'] = $dirObj->getFileList($d,$this->allowedExtn,false);
                        $config['last-modified'] = date('Y-m-d H:i:s',filemtime($d));
                        $config['basepath'] = basename($d);
                        array_push($configAll, $config);
                    }
                }
            }
            return array('carousels'=>$configAll);
        }
        
        public function findConfig($dir='')
        {
            $listCarousel = '';
            if(false===empty($dir) && true===is_dir($dir))
			{
				$this->dir = $dir;
			}
            $this->checkDirectoryFlow();
            $path = $this->getCarouselDir();
            
            $dirObj = new DirectoryParser\DirectoryParser();
            //$dirObj->getFileList($path, array('json'), true, 'carousel-store.json');
            $listCarousel = $dirObj->getFileList($this->dir, array('json'), true, 'carousel-store.json');
            return $listCarousel;
        }
        
		public function SetUpCarousel($carouselName, $allowedExtn=array())
		{
			$this->dir = $this->getCarouselDir();
			$this->checkDirectoryFlow();
			if(false === stripos($this->dir,"/$carouselName/"))
			{
				$this->dir .= $carouselName;
				$this->checkDirectoryFlow();
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
        
        /** Verifies if a carousel directory exists
         *  Important for updating the configuration for backward compatibility
         */
        public function verifyExist($carouselList, $carouselName='')
        {
            $carousels = array();
            if(true===isset($carouselList['carousels']) && true===isset($carousels)){
                foreach($carouselList['carousels'] as $carousel)
                {
                    if(true===empty($carouselName))
                    {
                        if(true===is_dir($carousel['path']))
                        {
                            array_push($carousels,$carousel);
                        }
                    } else {
                        if(true===is_dir($carousel['path']) && strtolower($carousel['name'])===strtolower($carouselName))
                        {
                            array_push($carousels,$carousel);
                        }
                    }                        
                }
            }
            return $carousels;
        }
        
        /** Function to check if more carousels exist
         *  than listed in the configuration
         *  Important for backward compatibility
         *  It also creates active directory, if it does not exist
         */
        public function checkMoreCarosuel($dir='')
        {          
            if(false===empty($dir) && true===is_dir($dir))
            {
                $this->dir = $dir;
                $this->checkDirectoryFlow();
                $activeDir = 'active';
                $dirObj = new DirectoryParser\DirectoryParser();
                $carouselDir = $dirObj->listDirectories($this->dir);
                foreach($carouselDir as $more)
                {
                    if(true===is_dir($more))
                    {
                        if(false===is_dir($more."/$activeDir"))
                        {
                            $dirObj->setCurrentDirectory($more);
                            $dirObj->addRelativeDirectory($activeDir);
                        }
                    }
                }
                return $carouselDir;
            }
        }
        
        public function uploadImages($dir, $carouselImages, $allowedExtn='')
        {
            if(false===empty($dir) && true===is_dir($dir))
            {
                $this->dir = $dir;
            }
            $this->checkDirectoryFlow();
            if(false===empty($allowedExtn) && true===is_array($allowedExtn))
            {
                $this->allowedExtn = $allowedExtn;
            }
            $fileObj = new FileUploader\FileUploader();
            try
            {
                $fileObj->uploadPictures($carouselImages, $this->allowedExtn, $this->dir,false,0);
            } 
            catch (Exception $e)
            {
                echo $e;
            }
            return true;
        }
        
        public function getUploadForm()
        {
            $fileObj = new FileUploader\FileUploader();
            $upForm = $fileObj->uploadForm();
            $upForm = str_replace($_SERVER['PHP_SELF'], '/admin/action/addImages', $upForm);
            return $upForm;
        }
        
        public function moveImages($source, $destination)
        {
            $filename = basename($source);
            if(false===empty($dir) && true===is_dir($dir))
            {
                $this->dir = $dir;
            }
            $this->checkDirectoryFlow();
            
            $dirObj = new DirectoryParser\DirectoryParser();
            $upForm = $dirObj->moveToDir($source, $destination.'/'.$filename);
        }
        
        public function generateThumb($imageSrc, $thumb_width, $thumb_height, $cropImage=false)
        {
            $fileObj = new FileUploader\FileUploader();
            $fileObj->generateThumb($imageSrc, $thumb_width, $thumb_height, $cropImage);
        }
	}
?>