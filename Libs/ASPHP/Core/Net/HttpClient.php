<?php
namespace ASPHP\Core\Net;
use \ASPHP\Core\Types\StaticClass;

/**
 * WebClient for HTTP requests
 * @requires class \ASPHP\Core\Types\StaticClass
 * @version 1.0
 * @author Vigan
 */
class HttpClient extends StaticClass
{
    /**
     * @param string $url 
     * @param array $httpheaders 
     * @param bool $verifySSL 
     * @throws \Exception 
     * @return string
     */
    public static function GET($url, $httpheaders = [], $verifySSL = false)
    {
        $webClient = curl_init();
        curl_setopt($webClient, CURLOPT_URL, $url);
        curl_setopt($webClient, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($webClient, CURLOPT_SSL_VERIFYPEER, false);
        if(count($httpheaders) > 0)
        {
            curl_setopt($webClient, CURLOPT_HTTPHEADER, $httpheaders);
        }

        $responseData = curl_exec($webClient);
        if(curl_errno($webClient)) 
		{
            throw new \Exception(curl_error($webClient), curl_errno($webClient));
	    }
        curl_close($webClient);

        return $responseData;
    }

    /**
     * @param string $url 
     * @param string $data 
     * @param array $httpheaders 
     * @param bool $verifySSL 
     * @throws \Exception 
     * @return string
     */
    public static function POST($url, $data, $httpheaders = [], $verifySSL = false)
    {
        $webClient = curl_init();
        curl_setopt($webClient, CURLOPT_URL, $url);
        curl_setopt($webClient, CURLOPT_POST, 1);
        curl_setopt($webClient, CURLOPT_POSTFIELDS, $data);
        curl_setopt($webClient, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($webClient, CURLOPT_SSL_VERIFYPEER, false);
        if(count($httpheaders) > 0)
        {
            curl_setopt($webClient, CURLOPT_HTTPHEADER, $httpheaders);
        }

        $responseData = curl_exec($webClient);
        if(curl_errno($webClient)) 
		{
            throw new \Exception(curl_error($webClient), curl_errno($webClient));
	    }
        curl_close($webClient);

        return $responseData;
    }
}