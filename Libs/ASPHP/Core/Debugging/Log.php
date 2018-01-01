<?php
namespace ASPHP\Core\Debugging;
use \ASPHP\Core\Types\StaticClass;
use \ASPHP\Core\IO\FileHandler;
use \ASPHP\Core\Configuration\Config;

/**
 * @requires class \ASPHP\Core\IO\FileHandler
 * @requires class \ASPHP\Core\Types\StaticClass
 * @requires class \ASPHP\Core\Configuration\Config
 * @version 1.2
 * @author Vigan
 */
class Log extends StaticClass
{
    /**
     * @param string $info
     * @param string $file
     */
    public static function put($info, $file = null)
    {
		$config = Config::Get();
		if ($file == null)
			$file = $config['environment']['directory']['logFile'];
        FileHandler::WriteFile($file, 'ab', date('Y-m-d h:i:s')." >>> ".$info.$config['environment']['newline']);
    }
}

?>