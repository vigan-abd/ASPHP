<?php
namespace ASPHP\Core\ErrorHandler;
use \ASPHP\Core\Types\StaticClass;
use \ASPHP\Core\Routing\Router;
use \ASPHP\Core\Web\HttpHandler;
use \ASPHP\Core\Configuration\Config;
use \ASPHP\Core\Debugging\Log;

/**
 * @requires class \ASPHP\Core\IO\FileHandler
 * @requires class \ASPHP\Core\Routing\Router
 * @requires class \ASPHP\Core\Types\StaticClass
 * @requires class \ASPHP\Core\Configuration\Config
 * @version 1.2
 * @author Vigan
 */
class Error extends StaticClass
{
    const PageHandle = 0,
          HeaderHandle = 1,
		  CliHandle = 2;
    public static $handle = Error::PageHandle;

    /**
     * Error Handler
     * @param int $errLevel
     * @param string $errMsg
     */
    public static function FatalError($errno, $errstr)
    {
		$config = Config::Get();
        $status = StatusMessage::GetInstance();
        $status->SetCode($errno);
        if($config['environment']['testMode'] || static::$handle == Error::CliHandle)
        {
            $status->SetMessage($errstr);
        }
        else
        {
            $status->SetMessage(HttpHandler::GetStatusMessage($errno));
        }
        $status->SetStatusType(StatusMessage::ERROR);
        if(static::$handle == Error::PageHandle)
        {
            Router::BuildRoute($config["routing"]["errorRoute"], $config["routing"]["errorAction"], [$status->GetCode(), $status->GetMessage()]);
        }
        else if(static::$handle == Error::HeaderHandle)
        {
            Error::ErrBackendHandler($status->GetCode(), $status->GetMessage());
        }
		else
		{
			echo "
FATAL ERROR >>> ".$status->GetCode().": ".$status->GetMessage()."
";
		}
		Log::put($status->GetCode()." : ".$status->GetMessage());
        exit();
    }

    /**
     * Writes the errors for ajax/REST calls
     * @param integer $errno
     * @param string $errstr
     */
    protected static function ErrBackendHandler($errno, $errstr)
    {
        if(Config::Get()['environment']['testMode'])
        {
            HttpHandler::SendHttpCodeHeader($errno, $errstr);
        }
        else
        {
            HttpHandler::SendHttpCodeHeader(404, "Bad Request");
        }
    }
}

?>