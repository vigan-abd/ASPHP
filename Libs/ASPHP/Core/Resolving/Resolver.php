<?php
namespace ASPHP\Core\Resolving;
use \ASPHP\Core\Configuration\Config;

/**
 * Resolves unloaded classes
 * @requires class \ASPHP\Core\Types\StaticClass
 * @version 1.0
 * @author Vigan
 */
class Resolver extends \ASPHP\Core\Types\StaticClass
{
    protected static $config = null;

    /**
	 * Resolves PHP Class
	 * @param string $classname
	 * @return string
	 */
    public static function Resolve($classname)
    {
		$root = Config::Get()["environment"]["directory"]["~"];
		if(file_exists($root.'/'.trim($classname, '/\\').'.php'))
		{
			require_once $root.'/'.trim($classname, '/\\').'.php';
			return $classname;
		}
		else if(file_exists($root.'/Libs/'.trim($classname, '/').'.php'))
		{
			require_once $root.'/Libs/'.trim($classname, '/\\').'.php';
			return $classname;
		}
		else if(!empty($GLOBALS['aliases'][$classname]) && file_exists($root.'/'.trim($GLOBALS['aliases'][$classname], '/\\').'.php'))
		{
			require_once $root.'/'.trim($GLOBALS['aliases'][$classname], '/\\').'.php';
			class_alias($GLOBALS['aliases'][$classname], $classname);
			return $GLOBALS['aliases'][$classname];
		}
		else if(!empty($GLOBALS['aliases'][$classname]) && file_exists($root.'/Libs/'.trim($GLOBALS['aliases'][$classname], '/\\').'.php'))
		{
			require_once $root.'/Libs/'.trim($GLOBALS['aliases'][$classname], '/\\').'.php';
			class_alias($GLOBALS['aliases'][$classname], $classname);
			return $GLOBALS['aliases'][$classname];
		}
		return "";
		//if(!isset(static::$config))
		//    static::$config = json_decode(file_get_contents(Config::Get()["environment"]["directory"]["~"].'/App_Start/resolver.config.json'), true);

		//if(key_exists($classname, static::$config['aliases']))
		//{
		//    require_once Config::Get()["environment"]["directory"]["~"].static::$config['paths'][static::$config['aliases'][$classname]];
		//    return static::$config['aliases'][$classname];
		//}
		//else
		//{
		//    foreach (static::$config['aliases'] as $k => $v)
		//    {
		//        if(preg_match("/".$k."/", $classname))
		//        {
		//            require_once Config::Get()["environment"]["directory"]["~"].static::$config['paths'][$v];
		//            return $v;
		//        }
		//    }
		//}
		//return "";
    }
}
?>