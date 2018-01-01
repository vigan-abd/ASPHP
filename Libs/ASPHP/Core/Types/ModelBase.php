<?php
namespace ASPHP\Core\Types;

/**
 * @version 1.0
 * @author Vigan
 */
abstract class ModelBase
{
	/**
	 * @param array $data
	 */
	public function __construct($data = [])
	{
		$this->Seed($data);
	}

    /**
	 * Maps values to class properties. Format ["prop" => val]
	 * @param array $values
	 */
    public function Seed($values)
    {
        foreach ($values as $key => $value)
        {
        	$this->__set($key, $value);
        }
    }

    /**
	 * @param string $name
	 * @throws \Exception
	 * @return mixed
	 */
    public function __get($name)
    {
        $name = trim($name);
        $props = get_class_vars(get_class($this));
        foreach ($props as $prop => $value)
        {
        	if($name == $prop)
            {
                return $value;
            }
        }
		return null;
    }

    /**
	 * @param string $name
	 * @param mixed $value
	 * @throws \Exception
	 * @return void
	 */
    public function __set($name, $value)
    {
        $name = trim($name);
        $props = get_class_vars(get_class($this));
        foreach ($props as $prop => $val)
        {
        	if($name == $prop)
            {
                $this->{$prop} = $value;
                return;
            }
        }
    }

    /**
	 * @param mixed $object
	 * @return boolean
	 */
    function Equals($object)
    {
        return $this->GetHashCode() == spl_object_hash($object);
    }

    /**
	 * @return string
	 */
    function GetHashCode()
    {
        return spl_object_hash($this);
    }

    /**
	 * @param string $method
	 * @return boolean
	 */
    public function HasMethod($method)
    {
        return method_exists($this, $method);
    }

    /**
	 * @param string $prop
	 * @return boolean
	 */
    public function HasProperty($prop)
    {
        return property_exists($this, $prop);
    }

    /**
	 * Returns an array listing property names
	 * @return array
	 */
    public function ListProperties()
    {
        $vars = get_class_vars(get_class($this));
        $props = [];
        foreach ($vars as $prop => $val)
        {
        	$props[] = $prop;
        }
        return $props;
    }
}
?>