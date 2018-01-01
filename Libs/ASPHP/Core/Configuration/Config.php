<?php
namespace ASPHP\Core\Configuration;
use \ASPHP\Core\Types\Lazy;
use \ASPHP\Core\Types\StaticClass;

/**
 * @requires \ASPHP\Core\Types\Configuration
 * @requires \ASPHP\Core\Types\Lazy
 * @requires \ASPHP\Core\Types\StaticClass
 * @version 1.0
 * @author Vigan
 */
class Config extends StaticClass
{
	/**
	 * @var \ASPHP\Core\Types\Lazy
	 */
	protected static $config;

	/**
	 * @return array
	 */
	public static function Get()
	{
		if(static::$config == null)
			static::$config = new Lazy(function()
			{
				return json_decode(file_get_contents($GLOBALS['webconfig']), true);
			});

		return static::$config->Value();
	}

	public static function InitGlobals()
	{
		require_once __DIR__.'/globals.php';
		global $_VIEWBAG;
		$_VIEWBAG['Title'] = "ASPHP Framework";
		$_VIEWBAG["Copy"] = "&copy; 2017";
	}
}
