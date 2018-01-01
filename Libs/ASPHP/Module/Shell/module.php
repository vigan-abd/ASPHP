<?php
require_once __DIR__.'/Commands/TestArgs.php';
require_once __DIR__.'/Commands/Sql2Class.php';
require_once __DIR__.'/Commands/Class2Sql.php';

$GLOBALS['shell_cmd'][\ASPHP\Module\Shell\Commands\TestArgs::signature] =
	\ASPHP\Module\Shell\Commands\TestArgs::class;
$GLOBALS['shell_cmd'][\ASPHP\Module\Shell\Commands\Sql2Class::signature] =
	\ASPHP\Module\Shell\Commands\Sql2Class::class;
$GLOBALS['shell_cmd'][\ASPHP\Module\Shell\Commands\Class2Sql::signature] =
	\ASPHP\Module\Shell\Commands\Class2Sql::class;
?>