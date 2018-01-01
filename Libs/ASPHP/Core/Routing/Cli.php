<?php
namespace ASPHP\Core\Routing;
use \ASPHP\Core\Types\StaticClass;
use \ASPHP\Core\Configuration\Config;
use \ASPHP\Core\ErrorHandler\Error;

/**
 * MVC Routing engine
 * @requires class \ASPHP\Core\Types\StaticClass
 * @requires class \ASPHP\Configuration\Config
 * @version 1.2
 * @author Vigan
 */
class Cli extends StaticClass
{
    public static function DispatchCommand()
    {
		Error::$handle = Error::CliHandle;
		if(!isset($GLOBALS['shell_cmd']))
			Error::FatalError(500, "No commands found on the app!");

		$args = static::ParseArgs();
		if(!isset($GLOBALS['shell_cmd'][$args['cmd']]))
			Error::FatalError(500, "Command not supported!");
		eval ("\$obj = new {$GLOBALS['shell_cmd'][$args['cmd']]}(); \$obj->run(\$args);");

	}

	public static function ParseArgs()
	{
		Error::$handle = Error::CliHandle;
		$args = $_SERVER['argv'];
		$parsed = [];
		$length = count($args);
		if($length < 2)
			Error::FatalError(500, "Please enter a command!");
		$parsed['cmd'] = $args[1];
		$k = 0;
		for ($i = 2; $i < $length; $i++)
		{
			if($args[$i][0] == '-')
			{
				if(($i + 1) == $length)
					throw new \Exception('Invalid arguments!');
				$parsed[$args[$i]] = $args[$i + 1];
				$i++;
			}
			else
			{
				$parsed[$k] = $args[$i];
				$k++;
			}
		}

		return $parsed;
	}
}