<?php
namespace ASPHP\Core\Routing;
use \ASPHP\Core\Types\StaticClass;
use \ASPHP\Core\Configuration\Config;

/**
 * MVC Routing engine
 * @requires class \ASPHP\Core\Types\StaticClass
 * @requires class \ASPHP\Configuration\Config
 * @version 1.2
 * @author Vigan
 */
class Router extends StaticClass
{
    /**
	 * @var array
	 */
    protected static $route = [];

    /**
	 * @return array
	 */
    public static function Current()
    {
        return static::$route;
    }

    public static function DispatchRoute()
    {
        if(empty($_REQUEST['request']))
            $_REQUEST['request'] = "";

        $args = explode('/', trim($_REQUEST['request'], '/'));//Get part request=$1 from htaccess, so get part after ?
        $argLength = count($args);
        if($argLength >= 2)
        {
            static::$route['Controller'] = $args[0];
            static::$route['Action'] = $args[1];
            static::$route['Params'] = [];
        }
        else if($argLength == 1)
        {
            static::$route['Controller'] = (!empty($args[0]) ? $args[0] : Config::Get()["routing"]["defaultRoute"]);
            static::$route['Action'] = Config::Get()["routing"]["defaultAction"];
            static::$route['Params'] = [];
        }
        else
        {
            static::$route['Controller'] = Config::Get()["routing"]["defaultRoute"];
            static::$route['Action'] = Config::Get()["routing"]["defaultAction"];
            static::$route['Params'] = [];
        }

        for ($i = 2; $i < $argLength; $i++)
        {
            static::$route['Params'][] = $args[$i];
        }

        if($_SERVER['REQUEST_METHOD'] == "POST")
        {
            foreach ($_POST as $k => $v)
            {
                if($k != 'Controller' && $k != 'Action')
                {
                    static::$route['Params'][$k] = $v;
                }
            }
        }
        else if($_SERVER['REQUEST_METHOD'] == "GET")
        {
            foreach ($_GET as $k => $v)
            {
                if($k != 'Controller' && $k != 'Action' && $k != 'request')
                {
                    static::$route['Params'][$k] = $v;
                }
            }
        }

        global $_ROUTE;
        require_once Config::Get()["environment"]["directory"]["~"]."/Controller/".static::$route['Controller']."Controller.php";
        $code = '
        $controller = new \\Controller\\'.static::$route['Controller'].'Controller();
        $controller->method = "'.static::$route['Action'].'";
        $controller->params = static::$route["Params"];
        $controller->ActionDispacher();';

        eval($code);
    }

    /**
	 * @param string $controller
	 * @param string $action
	 * @param array $params
	 */
    public static function BuildRoute($controller, $action, $params = [])
    {
		if (!headers_sent())
		{
			header("Location: ".static::BuildRouteString($controller, $action, $params));
			exit();
		}
    }


    /**
	 * @param string $controller
	 * @param string $action
	 * @param array $params
	 * @return string
	 */
    public static function BuildRouteString($controller, $action, $params = [])
    {
        $args = "";
        $i=0;
        $delimiter = "/";
        foreach($params as $k => $v)
        {
            if($i==0)
            {
                if(!is_int($k))
                {
                    $delimiter = "&";
                    $args = "?";
                }
            }

            if($delimiter == "&")
                $args .= (($i == 0) ? "" : $delimiter).$k."=".rawurlencode($v);
            else
                $args .= rawurlencode($v)."{$delimiter}";
            $i++;
        }

		return Config::Get()["environment"]["directory"]["webRoot"]."/{$controller}/{$action}/{$args}";
    }
}