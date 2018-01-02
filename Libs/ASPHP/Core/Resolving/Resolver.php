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
		if(file_exists(static::path_reslover($root.'/'.trim($classname, '/\\').'.php')))
		{
			require_once static::path_reslover($root.'/'.trim($classname, '/\\').'.php');
			return $classname;
		}
		else if(file_exists(static::path_reslover($root.'/Libs/'.trim($classname, '/').'.php')))
		{
			require_once static::path_reslover($root.'/Libs/'.trim($classname, '/\\').'.php');
			return $classname;
		}
		else if(!empty($GLOBALS['aliases'][$classname]) && file_exists(static::path_reslover($root.'/'.trim($GLOBALS['aliases'][$classname], '/\\').'.php')))
		{
			require_once static::path_reslover($root.'/'.trim($GLOBALS['aliases'][$classname], '/\\').'.php');
			class_alias($GLOBALS['aliases'][$classname], $classname);
			return $GLOBALS['aliases'][$classname];
		}
		else if(!empty($GLOBALS['aliases'][$classname]) && file_exists(static::path_reslover($root.'/Libs/'.trim($GLOBALS['aliases'][$classname], '/\\').'.php')))
		{
			require_once static::path_reslover($root.'/Libs/'.trim($GLOBALS['aliases'][$classname], '/\\').'.php');
			class_alias($GLOBALS['aliases'][$classname], $classname);
			return $GLOBALS['aliases'][$classname];
		}
		return "";
	}

	public static function path_reslover($path)
	{
		if(PHP_OS == 'WINNT')
		{
			return str_replace("/", "\\", $path);
		}
		else
		{
			return str_replace("\\", "/", $path);
		}
	}
}
?>