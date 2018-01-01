<?php
namespace ASPHP\Module\Shell\Commands;
use ASPHP\Core\Shell\IShellCommand;
use ASPHP\Core\Configuration\Config;
use ASPHP\Core\ErrorHandler\Error;

class Sql2Class implements IShellCommand
{
	const signature = 'sql_2class';
	/**
	 * sql_2class -m <model dir>
	 * @param array $args
	 */
	public function run(array $args)
	{
		if (empty($args['-m']))
			Error::FatalError(400, "sql_2class -m argument is required");
		$context = new \ASPHP\Module\Data\Entity\Schema\SchemaContext();
		$n = str_replace("/", "\\", trim($args['-m'], "/\\"));
		$m = str_replace("\\", "/", rtrim($args['-m'], "/\\"));
		$context->SetModelDir($m);
		$context->SetNamespace("\\".$n);
		$context->ScaffoldClasses();
	}
}
?>