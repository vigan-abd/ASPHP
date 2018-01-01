<?php
namespace ASPHP\Core\Configuration;
use \ASPHP\Core\Types\StaticClass;
use \ASPHP\Core\Routing\Router;
use \ASPHP\Core\Routing\Cli;

/**
 * @requires \ASPHP\Core\Types\Configuration
 * @requires \ASPHP\Core\Types\StaticClass
 * @version 1.0
 * @author Vigan
 */
class Boostrap extends StaticClass
{
	public static function init()
	{
		session_start();
		Config::InitGlobals();

		if (php_sapi_name() == "cli")
		{// In cli-mode
			$_SERVER['DOCUMENT_ROOT'] = __DIR__.'/../../../..';
		}

		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/App_Start/globals.php'))
		{
			require_once $_SERVER['DOCUMENT_ROOT'].'/App_Start/globals.php';
		}

		if(!Config::Get()['environment']['testMode'])
		{
			set_error_handler("\\ASPHP\\Core\\ErrorHandler\\Error::FatalError", E_ALL);
		}

		if (php_sapi_name() == "cli")
		{// In cli-mode
			Cli::DispatchCommand();
		}
		else
		{// Not in cli-mode
			Router::DispatchRoute();
		}
	}
}