<?php
namespace ASPHP\Module\Shell\Commands;
use ASPHP\Core\Shell\IShellCommand;

class TestArgs implements IShellCommand
{
	const signature = 'test_args';
	/**
	 * @param array $args 
	 */
	public function run(array $args)
	{
		print_r($args);
	}
}
?>