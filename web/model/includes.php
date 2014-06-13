<?php
    /** Store all the static data here
     *  Include in the controller
     *  Access using global variables
     */
    define ("CAROUSEL_DIR", str_replace('\\','/',dirname(dirname(dirname(__FILE__)))."/upload"));
    define ("CAROUSEL_URL", 'http://'.$_SERVER['HTTP_HOST'].
            str_replace(str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']),'',CAROUSEL_DIR));
    define ("WEB_DIR", str_replace('\\','/',dirname(dirname(__FILE__))."/views"));
    define ("WEB_URL", 'http://'.$_SERVER['HTTP_HOST'].
            str_replace(str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']),'',WEB_DIR));
    $currURL = $_SERVER['REQUEST_URI'];
    $currURL = str_replace('/admin','',$currURL);
    $commonTemplate = array(
        'headLayout' => array(
            'doctype' => '<title>',
            'title' => '</title>',
            'currentURL' => 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
            'styleSheet'=> '<link href="'.WEB_URL.'/css/style.css" rel="stylesheet"></link>',
            'jCarouselStyle'=> '',
            'jQuery' => WEB_URL.'/js/jquery-1.9.1.js',
            'jCarouselScript' => '',
            'jCarouselBasicScript' => '' 
        ),
    );
    
    function getPrevURL($times=0)
    {
        switch($times){
            case 0 : $prevUrl = 'http://'.$_SERVER['HTTP_HOST'].substr($_SERVER['REQUEST_URI'],0,(strlen($_SERVER['REQUEST_URI']) - strlen(strrchr($_SERVER['REQUEST_URI'], '/'))));
                     break;
            case 1 : $prevUrl = 'http://'.$_SERVER['HTTP_HOST'].substr($_SERVER['REQUEST_URI'],0,(strlen($_SERVER['REQUEST_URI']) - strlen(strrchr($_SERVER['REQUEST_URI'], '/'))));
                     $prevUrl = substr($prevUrl,0,(strlen($prevUrl) - strlen(strrchr($prevUrl, '/'))));
                     break;
            case 2 : 
                     break;
        }
        
        return $prevUrl;
    }
    function extractConfig($carouselName)
    {
        $path = CAROUSEL_DIR;
        $carouselObj = new pbalan\CarouselBuilder\CarouselBuilder();
        $dirObj = new pbalan\DirectoryParser\DirectoryParser();
        $listCarousel = $carouselObj->findConfig($path);
        if(true===is_array($listCarousel) && count($listCarousel)>0)
        {
            if(in_array($path.'/carousel-store.json', $listCarousel))
                $listCarousel = $path.'/carousel-store.json';
            else
                $listCarousel = '';
        }
        $content = "{\r\n}";
        $listCarousel = $carouselObj->createConfig(CAROUSEL_DIR, $content);
        $content = $dirObj->readFile($listCarousel);
        $carouselName = explode('_',basename($carouselName));
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
        $carouselList = json_decode($content, true);
        $carousels = $carouselObj->verifyExist($carouselList, $carouselName);
        $carousels = $carousels[0];
        return $carousels;
    }
    $blogPosts = array( 
        'default' => array(
            'login' => array(
                'title'     => 'Carousel Builder Admin',
                'topic'     => 'Carousel Builder Admin',
                'login'     => 'Please login',
                'jCarouselStyle'=> '<link href="'.WEB_URL.'/css/jCarousel-basic.css" rel="stylesheet"></link>'
            ),
            'homepage' => array(
                'title'     => 'Carousel Builder Admin',
                'topic'     => 'Carousel Builder Admin',
                'body'      => '<h3> Please login </h3>',
                'jCarouselStyle'=> '<link href="'.WEB_URL.'/css/jCarousel-basic.css" rel="stylesheet"></link>'
            ),
        ),
        '229' => array(
            'login' => array(
                'title'     => 'Carousel Builder Admin',
                'topic'     => 'Carousel Builder Admin',
                'body'      => '<h3> Please login </h3>',
                'jCarouselStyle'=> '<link href="'.WEB_URL.'/css/jCarousel-basic.css" rel="stylesheet"></link>'
            ),
            'homepage' => array(
                'title'     => 'Carousel Builder Admin',
                'topic'     => 'Carousel Builder Admin',
                'body'      => '<h3> Please login </h3>',
                'jCarouselStyle'=> '<link href="'.WEB_URL.'/css/jCarousel-basic.css" rel="stylesheet"></link>'
            ),
        ),
    );
    
    $formPosts = array( 
        'newCarousel' => array(
            'title'     => 'Carousel Builder Admin',
            'topic'     => 'Add a New Carousel',
            'formComponents' => array(
                'carouselName' => array(
                    'LABEL' => 'Please input a Carousel Name',
                    'PLACEHOLDER' => 'Enter a carousel name'
                ),
                'carouselWidth' => array(
                    'LABEL' => 'Please input a Carousel Width',
                    'PLACEHOLDER' => 'Enter a carousel width in pixels'
                ),
                'carouselHeight' => array(
                    'LABEL' => 'Please input a Carousel Height',
                    'PLACEHOLDER' => 'Enter a carousel height in pixels'
                ),
                'submit' => array(
                    'LABEL' => 'Submit',
                    'PLACEHOLDER' => 'Submit'
                )
            ),
            'jCarouselBasicScript' => WEB_URL.'/js/jCarousel-basic.js',
            'jCarouselStyle'=> '<link href="'.WEB_URL.'/css/jCarousel-basic.css" rel="stylesheet"></link>'
        ),
        'listCarousel' => array(
            'title'     => 'Carousel Builder Admin',
            'topic'     => 'Carousel Listing',
            'formComponents' => array(
                'carouselName' => array(
                    'LABEL' => 'Please input a Carousel Name',
                    'PLACEHOLDER' => 'Enter a carousel name'
                ),
                'submit' => array(
                    'LABEL' => 'Submit',
                    'PLACEHOLDER' => 'Submit'
                ),
            'editLabel' => 'Edit',
            'editLink'  => getPrevURL()."/edit",
            'deleteLabel' => 'Delete',
            'deleteLink'  => getPrevURL()."/delete"
            ),
            'jCarouselBasicScript' => WEB_URL.'/js/jCarousel-basic.js',
            'jCarouselStyle'=> '<link href="'.WEB_URL.'/css/jCarousel-basic.css" rel="stylesheet"></link>'
        ),
        'edit' => array(
            'title'     => 'Carousel Builder Admin',
            'topic'     => 'Edit Carousel',
            'formComponents' => array(
                'carouselName' => array(
                    'LABEL' => 'Please input a Carousel Name',
                    'PLACEHOLDER' => 'Enter a carousel name'
                ),
                'submit' => array(
                    'LABEL' => 'Submit',
                    'PLACEHOLDER' => 'Submit'
                ),
            'LABEL_NAME' => 'Carousel Name',
            'LABEL_ACTIVE_IMAGES' => 'Active Images',
            'LABEL_INACTIVE_IMAGES' => 'Inactive Images',
            'LABEL_UPLOAD_IMAGES' => 'Upload Images',
            'deleteLabel' => 'Delete',
            'deleteLink'  => getPrevURL(1)."/delete",
            'makeActive' => 'Activate',
            'makeInActive' => 'De-activate',
            'moveURL' => getPrevURL(1)."/move"
            ),
            'jCarouselStyle'=> '<link href="'.WEB_URL.'/css/jCarousel-basic.css" rel="stylesheet"></link>',
            'jCarouselBasicScript' => WEB_URL.'/js/jCarousel-basic.js',
        )
    );
?>