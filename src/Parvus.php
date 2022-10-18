<?php
    /**
     * Basic class of Parvus Framework
     */

    namespace Parvus;

    ini_set('display_errors',false);
    error_reporting(E_USER_ERROR | E_ERROR | E_COMPILE_ERROR | E_CORE_ERROR | E_RECOVERABLE_ERROR);

    use Illuminate\Container\Container;
    use Illuminate\Events\Dispatcher;
    use Symfony\Component\HttpFoundation\Request;
    use \Symfony\Component\Routing;
	use Whoops\Handler\Handler;

    class Parvus
    {
        private $environment = 'production';
        private $aApp = array();
        private $request,$routes,$controller;

        /**
         * Parvus constructor.
         * @param null $environment
         * @param null $connection
         */
        public function __construct($environment = NULL,$connection = NULL)
        {            
            /** Init Request */
            $this->request = Request::createFromGlobals();

            /** Load the app configuration */
            $this->aApp = include (path.'app/config/App.php');

            /** If have route files */
            if (file_exists(path.'app/config/Route.php'))
            {

                /** Init route collection */
                $this->routes = new Routing\RouteCollection();

                /** Load routes */
                foreach (include(path.'app/config/Route.php') as $id => $route)
                {

                    $this->routes->add($id,new Routing\Route($route[0],
                        $route[1],
                        $route[2]
                    ));

                }

                /** Create the context */
                $context = new Routing\RequestContext();
                $context->fromRequest($this->request);

                /** Create the matcher */
                $matcher = new \Parvus\URLMatcher($this->routes, $context);

                /** If has true, save the parameters */
                if ($aParameter = $matcher->collection($this->request->getPathInfo()))
                {

                    foreach ($aParameter as $name => $value)
                    {

                        /** if dont have underline */
                        if (mb_substr($name,0,1) != '_')
                        {

                            $_REQUEST[$name] =  $value;

                        }

                    }

                    /** Save the controller */
                    $this->controller = $aParameter['_controller'];

                }

            }

            /** Add the IP to trusted proxies */
            Request::setTrustedProxies(array($this->request->server->get('REMOTE_ADDR')));

            /** Define the base URL */
			$URL = $this->request->getScheme().'://'.$_SERVER['SERVER_NAME'].$this->request->getBaseUrl();

			if ($this->request->getPort() != 80)
            {
                $URL.= ':'.$this->request->getPort();
            }

            define ('url',$URL.'/');

            /** Load the app configuration */
            $this->aApp = include (path.'app/config/App.php');

            /** Init the session */
            session_start();

            if ($environment != NULL)
            {
                $this->setEnvironment($environment);
            }
            else
            {
                $this->environment();
            }
			
			/** Init Whoops */
			$whoops = new \Whoops\Run;
			if (environment !== 'production') {
				$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
			} else {
				$whoops->pushHandler(function ($exception, $inspector, $run) {
					echo 'ERROR';
					return Handler::DONE;
				});
			}
			$whoops->register();

            $this->database($connection);
            
            /** Include the app constant */
            include_once (path.'app/config/Constant.php');
        }

        /**
         * @param $environment
         */
        public final function setEnvironment ($environment)
        {

            define ('environment',$environment);

            $this->environment = $environment;

        }

        /**
         * Define the environment
         */
        private final function environment ()
        {

			/** When the server name is null and not have ssh_cliente, is local */
			if ($_SERVER['SERVER_NAME'] == NULL && $_SERVER['SSH_CLIENT'] == NULL)
			{
				$_SERVER['SERVER_NAME'] = 'localhost';
			}

            $aConfig = include(path.'app/config/Environment.php');

            foreach ($aConfig as $environment => $aValue)
            {
                foreach ($aValue as $value)
                {
                    if ($_SERVER['SERVER_NAME'] == $value)
                    {
                        $this->environment = $environment;
                    }
                }
            }

            /** Add a constant */
            define ('environment',$this->environment);
        }

        /**
         * Init the connection with database
         * @param null $prName
         */
        private final function database ($prName = NULL)
        {

            if(isset($this->environment))
            {

                $aConfig = include_once (path.'app/config/Database.php');

                /** Load the default driver */
                $driver = $aConfig['driver'];

                /** Load the default configuration */
                $aDefault = $aConfig['default'];

                /** Add the driver to the connection configuration */
                $aDefault['driver'] = $driver;

                /** Init the database manager */
                $database = new \Illuminate\Database\Capsule\Manager();

                /** Get the connections for the environment */
                foreach ($aConfig[$this->environment] as $name => $config)
                {

                    /** Each the connection attributes */
                    foreach (array('driver','host','user','password','database','charset','collation') as $field)
                    {

                        /** If the connection field is null, use the default value */
                        if ($config[$field] == NULL && $aDefault[$field] != NULL)
                        {

                            $config[$field] = $aDefault[$field];

                        }

                    }

                    /** Add a new connection */
                    $database->addConnection($config,($name == $driver && $prName == NULL) ? 'default' : ($prName != NULL ? $prName : $name));

                }

                /** Finish the connection */
                $database->setEventDispatcher(new Dispatcher(new Container()));
                $database->setAsGlobal();
                $database->bootEloquent();

            }

        }

        /**
         * Init the controller
         */
        public final function controller ()
        {
			$namespace 	= NULL;
			$controller = NULL;
			$method 	= NULL;

            /** Get the actual URL, first item is the controller, and the second the method */
            if ($this->request->getPathInfo() != '/')
            {
                $aTmp = array_filter(explode('/',$this->request->getPathInfo()));

                /** Get the method and controller, at the last 2 positions of URL */
                $method     = array_pop($aTmp);
                $controller = array_pop($aTmp);

				/** Get namespace from the first position */
				$namespace  = array_pop($aTmp);

				/** If namespace and controller is null, but have method, call index method */
				if ($namespace == NULL && $controller == NULL && $method != NULL)
				{
					$controller = $method;
					$method 	= 'index';
				}
            }
            else /** If not has controller and method, load the default from config */
            {
                $aConfig = include (path.'app/config/Controller.php');

                $controller = $aConfig['default']['controller'];
                $method     = $aConfig['default']['method'];
            }

			/** If namespace is null, set the default */
			if ($namespace == NULL)
			{
				$namespace = 'Controller';
			}

            $namespaceController = ucfirst(Text::camelCase($namespace)).'\\'.ucfirst(Text::camelCase($controller));
            $method 	         = 'action'.($_SERVER['REQUEST_METHOD'] == 'POST' ? 'Post' : 'Get').Text::camelCase($method,true);

            /** if have custom route controller */
            if ($this->controller != NULL)
            {

                $aController = explode('::',$this->controller);

                $namespaceController = array_shift($aController);
                $method = array_pop($aController);

            }

            $has404 = false;

            if (class_exists($namespaceController))
            {
                $class = new $namespaceController();

                if (method_exists($class,$method))
                {
                    $class->$method();
                } else {
                    $has404 = true;
                }
            } else {
                $has404 = true;
            }

            /** If has 404, and controller and method for error, call the function */
            if ($has404 && $this->aApp['error']['controller'] != NULL && $this->aApp['error']['method'] != NULL)
            {

                $class404  = $this->aApp['error']['controller'];
                $method404 = $this->aApp['error']['method'];

                if (!class_exists($class404) || !method_exists($class404,$method404))
                {

                    trigger_error('404 Error Class Not Found',E_CORE_ERROR);

                }

                $classController = new $class404;
                $has404 = $classController->$method404($namespace,$controller,$method);

            }

            /** If has not found the controller and method */
            if ($has404)
            {
                $view404 = new \Parvus\View();

				header('HTTP/1.0 404 Not Found');

                exit($view404->render($this->aApp['error']['404']));
            }
        }
    }

    /** Autoload class */
    spl_autoload_register(function($prName)
    {
        $aDirectory = array('app/controller/','app/model/');

        /** Load the config from app */
        $aArray = include (path.'app/config/App.php');

        /** Add the libs folders of app */
        foreach ($aArray['folder']['lib'] as $folder)
        {
            array_push($aDirectory,'app/'.$folder);
        }

        foreach ($aDirectory as $directory)
        {
            /** Add / at the directory name */
            if (substr($directory,-1) != '/')
            {
                $directory .= '/';
            }

            $prName = array_pop(explode('/',str_replace('\\','/',$prName)));

            if (file_exists(path.$directory.$prName.'.php'))
            {
                include_once (path.$directory.$prName.'.php');
            }
        }
    });
