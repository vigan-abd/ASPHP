<?php
namespace ASPHP\Core\Types;

/**
 * @version 1.0
 * @author Vigan
 */
class Lazy
{
    /**
	 * @var mixed
	 */
    protected $self = null;

    /**
	 * @var \Closure
	 */
    protected $func = null;

    /**
	 * @var mixed
	 */
    protected $func_args = null;

    /**
	 * @param array $args
	 * @return null
	 */
    protected function Load($args = []) { return null; }

    /**
	 * @param \Closure $loader
	 * @param array $func_args
	 */
    public function __construct(\Closure $loader = null, $func_args = [])
    {
        $this->func = $loader;
        $this->func_args = $func_args;
    }

    public function Value()
    {
        if($this->self == null)
		{
			$func = $this->func;
            $this->self = ($func != null ? $func($this->func_args) : $this->Load($this->func_args));
		}
        return $this->self;
    }
}
?>