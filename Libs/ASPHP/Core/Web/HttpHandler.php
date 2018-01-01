<?php
namespace ASPHP\Core\Web;
use ASPHP\Core\Types\StaticClass;
use ASPHP\Core\ErrorHandler\Error;

/**
 * @requires ASPHP\Core\Types\StaticClass
 * @requires ASPHP\Core\ErrorHandler\Error
 * @version 1.0
 * @author Vigan
 */
class HttpHandler extends StaticClass
{
	const HttpOK = 200;
    const HttpBadRequest = 400;//Invalid or incomplete input
    const HttpUnauthorized = 401;//User authentication required
    const HttpForbidden = 403;//Request formed correctly but the server refuses to carry it
    const HttpNotFound = 404;//Resource does not exist
    const HttpMethodNotAllowed = 405;
	const HttpNotAcceptable = 406; //Accept header not supported
    const HttpConflict = 409;//Attemp to put resrource into inconsistent state
    const HttpInternalServerError = 500;

	const MimeText = "text/*";
	const MimePlain = "text/plain";
	const MimeJson = "application/json";
	const MimeXml = "application/xml";
	const MimeHtml = "text/html";
	const MimeImg = "image/*";
	const MimePdf = "application/pdf";
	const MimePng = "image/png";
	const MimeJpeg = "image/jpeg";
	const MimeOctet = "application/octet-stream";

    public static function Http()
    {
        $args = func_get_args();
        if(!in_array($_SERVER['REQUEST_METHOD'], $args))
            Error::FatalError(401, 'Unauthorized');
    }

    public static function Https()
    {
        if(empty($_SERVER['HTTPS']))
        {
            if($_SERVER['SERVER_PORT'] != 443)
                Error::FatalError('Unauthorized', 401);
        }
    }

    /**
	 * @param integer $status
	 * @return string
	 */
    public static function GetStatusMessage($status)
    {
        switch ($status)
        {
            case 200: return "OK";
            case 400: return "Bad Request";
            case 401: return "Unauthorized";
            case 403: return "Forbidden";
            case 404: return "Not Found";
            case 405: return "Method Not Allowed";
			case 406: return "Not Acceptable";
            case 409: return "Conflict";
            case 500: return "Internal Server Error";
            default: return "Bad Request";
        }
    }

    /**
	 * Sets the HTTP Header
	 * @param integer $code
	 * @param string $errMsg
	 */
    public static function SendHttpCodeHeader($code, $msg)
    {
        header("HTTP/1.1 ".$code." ".$msg);
    }
}
?>