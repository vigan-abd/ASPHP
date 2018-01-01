<?php
namespace ASPHP\Module\Shell\Commands;
use ASPHP\Core\Shell\IShellCommand;
use ASPHP\Core\Configuration\Config;
use ASPHP\Core\ErrorHandler\Error;

class Class2Sql implements IShellCommand
{
	const signature = 'class_2sql';
	/**
	 * class_2sql -m <model dir> [-s <seed_file>]
	 * @param array $args
	 */
	public function run(array $args)
	{
		if (empty($args['-m']))
			Error::FatalError(400, "sql_2class -m argument is required");
		if(!empty($args['-s']) && file_exists(Config::Get()["environment"]["directory"]["~"].$args['-s']))
			$seed = require Config::Get()["environment"]["directory"]["~"].$args['-s'];
		else
			$seed = null;
		$m = str_replace("\\", "/", rtrim($args['-m'], "/\\"));
		$context = new \ASPHP\Module\Data\Entity\Schema\SchemaContext();
		$context->SetModelDir($m);
		$context->ScaffoldDatabase($seed);
	}
}
?>