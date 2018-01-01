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
class PenkInk implements IShellCommand
{
	const signature = 'pen_ink';

	/**
	 * @param array $args
	 */
	public function run(array $args)
	{
		echo '
Pen Ink Interpreter
Use # to determine end of the statement';
		$line = -1;
		while($line != "exit"){
		    if (PHP_OS == 'WINNT') {
		        echo '
pen_ink >>>
';
		        $line = stream_get_line(STDIN, 1024, "#");
		    } else {
		        $line = readline('
pen_ink >>>
');
		    }

		    eval($line);
		}
	}
}