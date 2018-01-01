<?php
namespace ASPHP\Core\Controller;
use \ASPHP\Core\ErrorHandler\Error;
use \ASPHP\Core\Web\HttpHandler;
use \ASPHP\Core\Serialization\XML\XmlSerializer;

/**
 * WebApiController short summary.
 *
 * WebApiController description.
 *
 * @version 1.0
 * @author Vigan
 */
abstract class WebApiController extends ControllerBase
{
	protected $forceMime = '';

	protected $supportedMimes = [
		HttpHandler::MimeText,
		HttpHandler::MimePlain,
		HttpHandler::MimeHtml,
		HttpHandler::MimeJpeg,
		HttpHandler::MimePng,
		HttpHandler::MimeImg,
		HttpHandler::MimePdf,
		HttpHandler::MimeOctet,
		HttpHandler::MimeJson,
		HttpHandler::MimeXml
	];

	public function ActionDispacher()
    {
		Error::$handle = Error::HeaderHandle;
        $this->method = ucwords($this->method);
        if(method_exists($this, $this->method))
        {
            $this->InvokeAttributes($this->method);
            $ret = $this->{$this->method}();
			if ($ret != null)
			{
				$this->ReturnFromAccept($ret);
			}
        }
        else
        {
            $this->DefaultAction();
        }
    }

	protected function DefaultAction()
	{
		Error::FatalError(400, "Bad Request");
	}

	protected function ForceMime($mime)
	{
		$this->forceMime = $mime;
	}

	protected function ReturnFromAccept($data)
	{
		if(!empty($this->forceMime))
		{
			$this->ReturnForMime($data, $this->forceMime);
			return;
		}
		$headers = getallheaders();
		$headers = getallheaders();
		if(!empty($headers['Accept']))
		{
			$accept = array_map(function ($x) { return trim($x); }, explode(",", $headers['Accept']));
			foreach($accept as $mime)
			{
				if(in_array($mime, $this->supportedMimes))
				{
					$this->ReturnForMime($data, $mime);
					return;
				}
			}
		}
		$this->ReturnForMime(null, 'X');//Will throw error
	}

	protected function ReturnForMime($data, $mime)
	{
		switch($mime)
		{
			case HttpHandler::MimeText: $this->ReturnAsPlain($data); break;
			case HttpHandler::MimePlain: $this->ReturnAsPlain($data); break;
			case HttpHandler::MimeHtml: $this->ReturnAsHtml($data); break;
			case HttpHandler::MimeJpeg: $this->ReturnAsFile($data, HttpHandler::MimeJpeg); break;
			case HttpHandler::MimePng: $this->ReturnAsFile($data, HttpHandler::MimePng); break;
			case HttpHandler::MimePdf: $this->ReturnAsFile($data, HttpHandler::MimePdf); break;
			case HttpHandler::MimeImg: $this->ReturnAsFile($data, HttpHandler::MimeOctet); break;
			case HttpHandler::MimeOctet: $this->ReturnAsFile($data, HttpHandler::MimeOctet); break;
			case HttpHandler::MimeJson: $this->ReturnAsJson($data); break;
			case HttpHandler::MimeXml: $this->ReturnAsXml($data); break;
			default:
				HttpHandler::SendHttpCodeHeader(HttpHandler::HttpNotAcceptable, "Not Acceptable");
				exit();
		}
	}

	protected function ReturnAsJson($data)
	{
		$this->SetMime(HttpHandler::MimeJson);
		echo json_encode($data, 0, 100000);
	}

	protected function ReturnAsXml($data)
	{
		$this->SetMime(HttpHandler::MimeXml);
		echo XmlSerializer::Serialze($data);
	}

	protected function ReturnAsPlain($data)
	{
		$this->SetMime(HttpHandler::MimePlain);
		if(is_array($data) || is_object($data))
			print_r($data);
		else
			echo $data;
	}

	protected function ReturnAsHtml($data)
	{
		$this->SetMime(HttpHandler::MimeHtml);
		if(is_array($data) || is_object($data))
			print_r($data);
		else
			echo $data;
	}

	protected function ReturnAsFile($file, $mime)
	{
		if(!file_exists($file))
			Error::FatalError(HttpHandler::HttpNotFound, "Not Found");

		$this->SetMime($mime);
		$data = file_get_contents($file);
		$length = filesize($file);
		header("Content-Type: {$mime}");
		header("Content-Length: {$length}");
		echo ($data);
	}

	protected function SetMime($mime)
	{
		header("Content-Type: {$mime}");
	}
}