<?php
$GLOBALS['webconfig'] = __DIR__.'/web.config.json';

$GLOBALS['aliases'] =
[
	"HttpHandler" => \ASPHP\Core\Web\HttpHandler::class,
	"Router" => \ASPHP\Core\Routing\Router::class,
	"Log" => \ASPHP\Core\Debugging\Log::class,
	"Debug" => \ASPHP\Core\Debugging\Debug::class,
	"Error" => \ASPHP\Core\ErrorHandler\Error::class,
	"StatusMessage" => \ASPHP\Core\ErrorHandler\StatusMessage::class,
];
?>