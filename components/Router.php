<?php

class Router
{
    public $routes;


    public function __construct()
    {
        $this->routes = include ROOT . '/config/routes.php';
    }

    public function start()
    {

        $url = $this->getRequest();

        list($controller, $action, $param) = $this->getSegments($url);
        $file = ROOT . "/controllers/$controller.php";

        if (file_exists($file)) {
            require_once $file;
        } else {
            PageError::error404();
            return false;
        }

        $controllerObj = new $controller();
        call_user_func_array(array($controllerObj, $action), $param);

        return true;
    }

    private function getRequest()
    {
        $url = trim($_SERVER['REQUEST_URI'], '/');
        if (!empty($url)) {
            return $url;
        }
        return;
    }

    private function getSegments($url)
    {
        if (empty($url)) {
            $controller = 'SiteController';
            $action = 'indexAction';
            $param = [];
        } else {
            $segments = explode('/', $url);
            $controller = ucfirst(array_shift($segments)) . 'Controller';
            $action = array_shift($segments) . 'Action';
            $param = $segments;
        }

        return [$controller, $action, $param];
    }
}