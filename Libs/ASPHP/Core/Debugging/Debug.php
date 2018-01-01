<?php
namespace ASPHP\Core\Debugging;
use \ASPHP\Core\Types\StaticClass;

/**
 * @requires class \ASPHP\Core\Types\StaticClass
 * @version 1.2
 * @author Vigan
 */
class Debug extends StaticClass
{
    /**
	 * @param mixed $data
	 */
    public static function Dump($data)
    {
		$data = func_get_args();
		foreach($data as $var)
		{
			static::DumpVar($var);
		}
    }

    /**
	 * @param mixed $data
	 */
    public static function Breakpoint($data)
    {
		$data = func_get_args();
		foreach($data as $var)
		{
			static::DumpVar($var);
		}
		exit();
    }

	protected static function DumpVar($data)
	{

		echo "
<pre style=\"
	display: block;
    margin: 0 0 10px;
    font-size: 13px;
    line-height: 1.42857143;
    word-break: break-all;
    word-wrap: break-word;
    border: 1px solid #ccc;
    background-color: rgba(238,238,238,.35);
    border-radius: 3px;
    padding: 10px;
    box-shadow: 0 1px 1px rgba(0,0,0,.12);
    color: #000000;
    overflow: hidden;
\">";
		if(is_object($data))
		{
			$class = get_class($data);
			$reflector = new \ReflectionClass($class);
			echo "
File: >>> ".$reflector->getFileName()."
Instance >>>
"; print_r($data); echo "
Methods >>>
".implode("\n", array_map(function ($x) { return "\t{$x},"; }, get_class_methods($data)));
		}
		else
		{
			echo "
".gettype($data)." >>>
"; print_r($data);
		}

		echo "
</pre>";
	}
}

?>