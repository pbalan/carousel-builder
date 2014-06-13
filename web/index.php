<?php
	
	/**	This is the admin panel to the Carousel Widget
	 *  You could apply customizations here
	 *	@Author: Prashant
	 *	@License: MIT <http://github.com/pbalan/carousel-builder/LICENSE.md>
	 *	http://github.com/pbalan/carousel-builder.git
	 */
     
    require_once __DIR__.'/../vendor/autoload.php';
    require_once __DIR__.'/model/includes.php';
    //require_once dirname(dirname(__FILE__))."/src/pbalan/CarouselBuilder/CarouselBuilder.php";
    
    global $blogPosts;
    global $commonTemplate;
    global $formPosts;
    
    $currURL = $_SERVER['REQUEST_URI'];
    $currURL = str_replace('/admin','',$currURL);
    
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\HttpFoundation\Cookie;
    use pbalan\DirectoryParser;
    use pbalan\CarouselBuilder;
    
    $app = new Silex\Application();
    $app['debug'] = true;    
    
    //echo __DIR__.'/views'; exit;
    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/views/twig',
    ));
    
    $app->get('/admin', function (Silex\Application $app) use ($commonTemplate, $blogPosts) {
        $request = $app['request'];
        $cookies = $request->cookies;
        if(false===$cookies->has("sign"))
        {
            $id = 'login';
            $post = $blogPosts['default'][$id];
            $display = array(
                'pageTitle' => "{$post['title']}",
                'topic'     => "{$post['topic']}",
                'styleSheet' => WEB_URL.'/css/style.css',
                'jQuery'    => $commonTemplate["headLayout"]["jQuery"],
                'login' => "{$post['login']}",
                'jCarouselStyle' => "{$post['jCarouselStyle']}",
                'sign' => 0,
                'WEB_URL' => getPrevURL()
            );
        }
        else if(true===$cookies->has("sign") && $cookies->get("sign") == 1)
        {
            $id = 'homepage';
            $post = $blogPosts['default'][$id];
            $display = array(
                'pageTitle' => "{$post['title']}",
                'topic'     => "{$post['topic']}",
                'styleSheet' => WEB_URL.'/css/style.css',
                'jQuery'    => $commonTemplate["headLayout"]["jQuery"],
                'login' => "{$post['login']}",
                'jCarouselStyle' => "{$post['jCarouselStyle']}",
                'sign'      => 1,
                'WEB_URL' => getPrevURL()
            );
        }
        if(true===isset($blogPosts[$id]['formComponents']) && true===is_array($blogPosts[$id]['formComponents']))
        {
            foreach($blogPosts[$id]['formComponents'] as $key => $val)
            {
                if(true===is_array($val)){
                    foreach($val as $k => $v)
                    {
                        $display = array_merge($display, array($key."_".$k => $v));
                    }
                } else {
                    $display = array_merge($display, array($key => $val));
                }
            }
        }
        if(false===$cookies->has("sign"))
        {
            return $app['twig']->render('login.twig', $display);
        }
        else if(true===$cookies->has("sign") && $cookies->get('sign') == 1)
        {
            return $app->redirect('admin/action/listCarousel');
        }
    });
    
    $app->post('/admin',function (Silex\Application $app, Request $request){
        if('POST' == $request->getMethod())
        {
            $username = $request->get('username');
            $password = $request->get('password');
            
            $auth = dirname(CAROUSEL_DIR).'/auth.json';
            $dirObj = new pbalan\DirectoryParser\DirectoryParser();
            $content = $dirObj->readFile($auth);
            $contentJSON = json_decode($content, true);
            $contentJSON = $contentJSON["users"];
            foreach($contentJSON as $user)
            {
                //echo $user["username"]. ", pass: ".$user["password"]."<br />";
                if($username===$user["username"] && $password === $user["password"])
                {
                    $dt = new \DateTime();
                    $dt->modify("+1 year");
                    $cookie = new Cookie("sign", "1", $dt);
                    $response = new RedirectResponse("admin", 302);
                    $response->headers->setCookie($cookie);
                    return $response;
                }
            }
            
            return $app->redirect("admin");
        }
    });
    $app->get('/admin/action/{id}/{carouselName}', function (Silex\Application $app, $id, $carouselName) use ($commonTemplate, $formPosts) {
        if (!isset($formPosts[$id])) {
            $app->abort(404, "Action $id does not exist.");
        }
        $post = $formPosts[$id];
        $commonTemplate['headLayout']['styleSheet'] = str_replace("/admin/action/{$id}/{$carouselName}", '', $commonTemplate['headLayout']['currentURL']);
        $display = array(
            'pageTitle' => "{$post['title']}",
            'topic'     => "{$post['topic']}",
            'styleSheet' => $commonTemplate["headLayout"]["styleSheet"].'/views/css/style.css',
            'jQuery'    => $commonTemplate["headLayout"]["jQuery"],
            'jCarouselStyle' => "{$post['jCarouselStyle']}",
            'jCarouselBasicScript' => "{$post['jCarouselBasicScript']}",
            'sign'      => 1,
            'WEB_URL' => getPrevURL()
        );
        if('edit'===$id){
            $carousels = extractConfig($carouselName);
            $carouselObj = new pbalan\CarouselBuilder\CarouselBuilder();
            foreach($carousels as $key=> $caro)
            {
                if($key==='active' || $key==='inactive'){
                    foreach($caro as $k => $v){
                        $carousels[$key][$k] = str_replace(CAROUSEL_DIR, CAROUSEL_URL, $carousels[$key][$k]);
                    }
                }
            }
            $display['carouselWidth'] = $carousels["carouselWidth"];
            $display['carouselHeight'] = $carousels["carouselHeight"]; 
            $display['carousel'] = $carousels;
            
            $upForm = $carouselObj->getUploadForm();
            $display['uploadForm'] = $upForm;
        }
        if(true===isset($formPosts[$id]['formComponents']) && true===is_array($formPosts[$id]['formComponents']))
        {
            foreach($formPosts[$id]['formComponents'] as $key => $val)
            {
                if(true===is_array($val)){
                    foreach($val as $k => $v)
                    {
                        $display = array_merge($display, array($key."_".$k => $v));
                    }
                } else {
                    $display = array_merge($display, array($key => $val));
                }
            }
        }
        
        return $app['twig']->render($id.'.twig', $display);
    });
    
    $app->get('/admin/action/{id}', function (Silex\Application $app, $id) use ($commonTemplate, $formPosts) {
        if (!isset($formPosts[$id])) {
            $app->abort(404, "Action $id does not exist.");
        }
        $post = $formPosts[$id];
        $commonTemplate['headLayout']['styleSheet'] = str_replace("/admin/action/{$id}", '', $commonTemplate['headLayout']['currentURL']);
        $display = array(
            'pageTitle' => "{$post['title']}",
            'topic'     => "{$post['topic']}",
            'styleSheet' => $commonTemplate["headLayout"]["styleSheet"].'/views/css/style.css',
            'jQuery'    => $commonTemplate["headLayout"]["jQuery"],
            'jCarouselStyle' => "{$post['jCarouselStyle']}",
            'jCarouselBasicScript' => "{$post['jCarouselBasicScript']}",
            'sign' => 1,
            'WEB_URL' => getPrevURL(1)
        );
        if('listCarousel'===$id){
            $path = CAROUSEL_DIR;
            $dirObj = new pbalan\DirectoryParser\DirectoryParser();
            $carouselObj = new pbalan\CarouselBuilder\CarouselBuilder();
            $listCarousel = $carouselObj->findConfig($path);
            if(true===is_array($listCarousel) && count($listCarousel)>0)
            {
                if(in_array($path.'/carousel-store.json', $listCarousel))
                    $listCarousel = $path.'/carousel-store.json';
                else
                    $listCarousel = '';
            }
            if(empty($listCarousel))
            {
                $content = "{\r\n}";
                $listCarousel = $carouselObj->createConfig(CAROUSEL_DIR, $content);
                //echo "carousel-store.json configuration file was not found on the system"; exit;
            }
            $content = $dirObj->readFile($listCarousel);
            $carouselList = json_decode($content, true);
            $carousels = $carouselObj->verifyExist($carouselList);
            $moreCarousels = $carouselObj->checkMoreCarosuel(CAROUSEL_DIR);
            $display['carousels'] = $carousels;
        }
        if(true===isset($formPosts[$id]['formComponents']) && true===is_array($formPosts[$id]['formComponents']))
        {
            foreach($formPosts[$id]['formComponents'] as $key => $val)
            {
                if(true===is_array($val)){
                    foreach($val as $k => $v)
                    {
                        $display = array_merge($display, array($key."_".$k => $v));
                    }
                } else {
                    $display = array_merge($display, array($key => $val));
                }
            }
        }
        
        return $app['twig']->render($id.'.twig', $display);
    });
    
    $app->post('/admin/action/newCarousel',function (Silex\Application $app, Request $request){
        $dest = CAROUSEL_DIR;
        // create active directory to differentiate images which are currently required to show on carousel
        $activeDir = 'active';
        $dest = str_replace('\\','/',$dest);
        
        if('POST' == $request->getMethod())
        {
            $carouselName = $request->get('carouselName');
            $carouselWidth = $request->get('carouselWidth');
            $carouselHeight = $request->get('carouselHeight');
        }
        if(true===empty($carouselName))
        {
            return 'carouselName cannot be empty';
        } else {
            $carouselName = $carouselName.'_'.$carouselWidth.'x'.$carouselHeight;
            $carouselObj = new pbalan\CarouselBuilder\CarouselBuilder($carouselName, $dest);
            if(true===$carouselObj->getError())
            {
                echo "carousel with name $carouselName exists! Please provide a different name.";
                exit;
            }
            $dest = $carouselObj->getCarouselDir();
            $dirObj = new pbalan\DirectoryParser\DirectoryParser($dest);
            $dirObj->addRelativeDirectory($activeDir);
            return $app->redirect("admin/action/edit/$carouselName");
        }
    });
    $app->post('/admin/action/move',function (Silex\Application $app, Request $request){
        $activeDir = 'active';
        if('POST' == $request->getMethod())
        {
            $carouselPath = CAROUSEL_DIR;
            $carouselName = $request->get('carouselName');
            $imagePath = $request->get('imagePath');
            $moveTo = $request->get('moveTo');
        }
        
        if(true===empty($imagePath))
        {
            return 'imagePath cannot be empty';
        } else {
            if(true===empty($moveTo)){
                return 'moveTo cannot be empty';
            } else {
                $imagePath = str_replace(CAROUSEL_URL, CAROUSEL_DIR, $imagePath);
                
                $carouselObj = new pbalan\CarouselBuilder\CarouselBuilder();
                if($moveTo==='active')
                {
                    $source = $imagePath;
                    $destination = $carouselPath.'/'.$carouselName.'/'.$activeDir;
                }
                else
                {
                    $source = $imagePath;
                    $destination = $carouselPath.'/'.$carouselName;
                }
                $carouselObj->moveImages($source, $destination);
            }
        }
        return 'ok';
    });
    
    $app->post('/admin/action/addImages',function (Silex\Application $app, Request $request){
        
        if('POST' == $request->getMethod())
        {
            $carouselPath = $request->get('carouselPath');
            $carouselName = $request->get('carouselName');
            $carouselImages = $_FILES;
            $overlayText = $request->get('overlayText');
        }
        if(true===empty($carouselPath))
        {
            return 'carouselPath cannot be empty';
        } else {
            if(true===empty($carouselImages)){
                return 'carouselImages cannot be empty';
            } else {
                $carouselObj = new pbalan\CarouselBuilder\CarouselBuilder();
                $carouselObj->uploadImages($carouselPath, $carouselImages);
                $carousels = extractConfig($carouselName, $overlayText);
                $thumb_width = $carousels["carouselWidth"];
                $thumb_height = $carousels["carouselHeight"];
                $carouselPath = $carouselObj->checkDirectoryFlow($carouselPath);
                for($i = 0; $i<=count($carouselImages["file"]["name"]); $i++)
                {
                    $imageName = $carouselImages["file"]["name"][$i];
                    
                    $carouselObj->generateThumb($carouselPath.$imageName, $thumb_width, $thumb_height, false);
                }
            }
        }
        return $app->redirect("edit/{$carouselName}");
    });
    $app->get('/admin/{id}', function (Silex\Application $app, $id) use ($blogPosts) {
        if (!isset($blogPosts[$id])) {
            $app->abort(404, "Post $id does not exist.");
        }
        $commonTemplate['headLayout']['styleSheet'] = str_replace("/admin/{$id}", '', $commonTemplate['headLayout']['currentURL']);
        $post = $blogPosts[$id];
        $output = '';
        $output .= $commonTemplate['headLayout']['doctype']."{$post['title']}".$commonTemplate['headLayout']['title'];
        $output .= '<link href="'.$commonTemplate["headLayout"]["styleSheet"].'/views/css/style.css" rel="stylesheet" />';
        $output .= '<script src="'.$commonTemplate["headLayout"]["jQuery"].'>';
        $output .= "<h1>{$post['title']}</h1>";
        $output .= "<p>{$post['body']}</p>";
        
        return $output;
    });
    
    $app->get('/admin/{id}/{pg}', function (Silex\Application $app, $id, $pg) use ($blogPosts, $commonTemplate) {
        if (!isset($blogPosts[$id])) {
            $app->abort(404, "Post $id does not exist.");
        }
        $post = $blogPosts[$id][$pg];
        $commonTemplate['headLayout']['styleSheet'] = str_replace("/admin/{$id}/{$pg}", '', $commonTemplate['headLayout']['currentURL']);
        $output = '';
        $output .= $commonTemplate['headLayout']['doctype'].$blogPosts['default']['homepage']['title'].$commonTemplate['headLayout']['title'];
        $output .= '<link href="'.$commonTemplate["headLayout"]["styleSheet"].'/views/css/'."{$id}".'/style.css" rel="stylesheet" />';
        $output .= "<h1>{$post['title']}</h1>";
        $output .= "<p>{$post['body']}</p>";
        
        return $output;
    });
    
    
    
    $app->get('/hello/{name}', function ($name) use ($app) {
        return 'Hello '.$app->escape($name);
    });
    
    
    $app->run();