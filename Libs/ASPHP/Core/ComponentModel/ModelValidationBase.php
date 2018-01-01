<?php
namespace ASPHP\Core\ComponentModel;
use \ASPHP\Core\Attribute\AttributeReader;
use \ASPHP\Core\Types\ModelBase;

/**
 * @requires class \ASPHP\Core\Attribute\AttributeReader
 * @requires class \ASPHP\Core\Types\ModelBase
 * @author Vigan
 * @version 1.0
 */
abstract class ModelValidationBase
{
    /**
     * @var AttributeReader
     */
    protected $attr;

    protected $methods = ['required', 'minlength', 'maxlength', 'rangelength', 'min', 'max', 'range', 'email', 'url', 'date', 'dateISO', 'number', 'digits', 'equalTo', 'alphanumeric', 'creditcard', '\integer', 'ipv4', 'ipv6', 'lettersonly', 'letterswithbasicpunc', 'regex', 'extension'];

    public function __construct()
    {
        $this->attr = new AttributeReader(get_class($this));
    }

    public abstract function Validate(ModelBase $model = null);

    public function SetModel(ModelBase $model)
    {
        $this->attr->SetClass(get_class($model));
    }

    public abstract function ValidateProperty($prop);

    public abstract function ValidateRule($field, $rule, $params = []);

    protected abstract function required($val = null);
    protected abstract function minlength($min, $val = null);
    protected abstract function maxlength($max, $val = null);
    protected abstract function rangelength($min, $max, $val = null);
    protected abstract function min($min, $val = null);
    protected abstract function max($max, $val = null);
    protected abstract function range($min, $max, $val = null);
    protected abstract function email($val = null);
    protected abstract function url($val = null);
    protected abstract function date($val = null);
    protected abstract function dateISO($val = null);
    protected abstract function number($val = null);
    protected abstract function digits($val = null);
    protected abstract function equalTo($field, $val = null);
    protected abstract function alphanumeric($val = null);
    protected abstract function creditcard($val = null);
    protected abstract function integer($val = null);
    protected abstract function ipv4($val = null);
    protected abstract function ipv6($val = null);
    protected abstract function lettersonly($val = null);
    protected abstract function letterswithbasicpunc($val = null);
    protected abstract function regex($regex, $val = null);
    protected abstract function extension($ext, $val = null);
}
?>