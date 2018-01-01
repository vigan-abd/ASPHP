<?php
namespace ASPHP\Core\Shell\Cmd;
use \ASPHP\Core\Shell\IShellCommand;

/**
 * Lister short summary.
 *
 * Lister description.
 *
 * @version 1.0
 * @author Vigan
 */
class Lister implements IShellCommand
{
	const signature = 'ls';

	/**
	 * @param array $args
	 */
	public function run(array $args)
	{
		foreach($GLOBALS['shell_cmd'] as $cmd => $class)
		{
			echo" > {$cmd}
";
		}
	}
}