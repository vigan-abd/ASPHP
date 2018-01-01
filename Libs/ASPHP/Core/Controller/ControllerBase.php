<?php
namespace ASPHP\Core\Controller;
use \ASPHP\Core\View\View;
use \ASPHP\Core\Attribute\AttributeReader;
use \ASPHP\Core\Resolving\Resolver;

/**
 * @requires \ASPHP\Core\Controller\IController
 * @requires \ASPHP\Core\View\View;
 * @requires \ASPHP\Core\Attribute\AttributeReader;
 * @requires \ASPHP\Core\Resolving\Resolver
 * @author Vigan
 * @version 1.3
 */
abstract class ControllerBase implements IController
{
    /**
	 * @var \ASPHP\Core\Attribute\AttributeReader
	 */
    protected $attr;

    public $method, $params;

    /**
	 * @var \ASPHP\Core\View\View
	 */
    public $view;

    /**
	 * Maps the URL to action
	 */
    public function ActionDispacher()
    {
        $this->view = new View();
        $this->method = ucwords($this->method);
        if(method_exists($this, $this->method))
        {
            $this->InvokeAttributes($this->method);
            $this->{$this->method}();
        }
        else
        {
            $this->DefaultAction();
        }
    }

    /**
	 * Injects the invoke methods before executing the method
	 * @param string $method
	 */
    protected function InvokeAttributes($method)
    {
        if($this->attr == null)
            $this->attr = new AttributeReader(get_class($this));
        $attr = $this->attr->GetMethodAttributes($method);
        $length = count($attr);
        for ($i=0; $i < $length; $i++)
        {
            if($attr[$i]['key'] == 'invoke')
			{
				$class = Resolver::Resolve(explode("::", $attr[$i]['val'])[0]);
                eval("use {$class};{$attr[$i]['val']};");
			}
        }
    }

    abstract protected function DefaultAction();
}
?>