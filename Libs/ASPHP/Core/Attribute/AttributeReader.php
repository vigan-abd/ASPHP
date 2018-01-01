<?php
namespace ASPHP\Core\Attribute;

class AttributeReader
{
    /**
     * @var \ReflectionClass
     */
    protected $reflector;

    public function __construct($class)
    {
        $this->reflector = new \ReflectionClass($class);
    }

    /**
     * @param \string $doc
     * @return \string
     */
    protected function ExtractAttributes($doc)
    {
        $patterns = [];
        $patterns[0] = '/\s*\/\*+/';//'/\s*\/\*+\n/';
        $patterns[1] = '/\s*\*+\s+/';
        $patterns[2] = '/\s*\*\//';
        $doc = trim(preg_replace($patterns, "", $doc), "@");
        $doc = explode("@", $doc);
        $length = count($doc);
        for($i=0; $i<$length; $i++)
        {
            $tmp = explode(' ', $doc[$i], 2);
            $doc[$i] = ["key" => $tmp[0], "val" => (empty($tmp[1]) ? $tmp[0] : $tmp[1])];
        }
        return $doc;
    }

    public function SetClass($class)
    {
        $this->reflector = new \ReflectionClass($class);
    }

    /**
     * Returns the reflection class name
     * @return \string
     */
    public function GetClass()
    {
        return $this->reflector->getName();
    }

    /**
     * Returns the reflector component
     * @return \ReflectionClass
     */
    public function GetReflector()
    {
        return $this->reflector;
    }

    /**
     * @param \string $method
     * @return \ReflectionMethod
     */
    public function GetMethod($method)
    {
        return $this->reflector->getMethod($method);
    }

    /**
     * @param \string $method
     * @return \string
     */
    public function GetMethodDoc($method)
    {
        return $this->GetMethod($method)->getDocComment();
    }

    /**
     * @param \string $method
     * @return \string
     */
    public function GetMethodAttributes($method)
    {
        $doc = $this->GetMethodDoc($method);
        return $this->ExtractAttributes($doc);
    }

    /**
     * @param \string $prop
     * @return \ReflectionProperty
     */
    public function GetProperty($prop)
    {
        return $this->reflector->getProperty($prop);
    }

    /**
     * @param \string $prop
     * @return \string
     */
    public function GetPropertyDoc($prop)
    {
        return $this->GetProperty($prop)->getDocComment();
    }

    /**
     * @param \string $prop
     * @return \string
     */
    public function GetPropertyAttributes($prop)
    {
        $doc = $this->GetPropertyDoc($prop);
        return $this->ExtractAttributes($doc);
    }

	/**
	 * @return string
	 */
	public function GetClassAttributes()
	{
		return $this->ExtractAttributes($this->reflector->getDocComment());
	}

	/**
	 * @return array
	 */
	public function ReadProperties()
	{
		return $this->reflector->getProperties();
	}
}
?>