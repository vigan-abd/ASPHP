<?php
namespace ASPHP\Core\Shell;

/**
 * @version 1.0
 * @author Vigan
 */
interface IShellCommand
{
	public function run(array $args);
}
