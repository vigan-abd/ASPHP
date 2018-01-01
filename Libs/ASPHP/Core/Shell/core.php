<?php
require_once __DIR__.'/IShellCommand.php';
require_once __DIR__.'/Cmd/Lister.php';
require_once __DIR__.'/Cmd/PenInk.php';
$GLOBALS['shell_cmd'][\ASPHP\Core\Shell\Cmd\Lister::signature] =
	\ASPHP\Core\Shell\Cmd\Lister::class;
$GLOBALS['shell_cmd'][\ASPHP\Core\Shell\Cmd\PenkInk::signature] =
	\ASPHP\Core\Shell\Cmd\PenkInk::class;
?>