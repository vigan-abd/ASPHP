<?php
namespace ASPHP\Core\ComponentModel;
use \ASPHP\Core\Attribute\AttributeReader;
use \ASPHP\Core\Types\ModelBase;

/**
 * @requires class \ASPHP\Core\ComponentModel\ModelValidationBase
 * @requires class \ASPHP\Core\Attribute\AttributeReader
 * @requires class \ASPHP\Core\Types\ModelBase
 * @version 1.0
 * @author Vigan
 */
class ClientModelValidation extends ModelValidationBase
{

    protected $formID = "";
    protected $fields = [];
    protected $messages = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function ReadType($prop)
    {
        $attr = $this->attr->GetPropertyAttributes($prop);
        $length = count($attr);
        for($i=0; $i<$length; $i++)
        {
            if($attr[$i]['key'] == 'var')
                return $attr[$i]['val'];
        }
        return 'mixed';
    }

    public function ReadAttributes($prop)
    {
        return $this->attr->GetPropertyAttributes($prop);
    }

    public function ValidateProperty($prop)
    {
        $attr = $this->attr->GetPropertyAttributes($prop);
        $length = count($attr);
        for ($i=0; $i < $length; $i++)
        {
            switch($attr[$i]['key'])
            {
                case 'rangelength':
                case 'range':
                    $this->ValidateRule($prop, $attr[$i]['key'], explode(',', $attr[$i]['val'])); break;
                default:
                    $this->ValidateRule($prop, $attr[$i]['key'], [$attr[$i]['val']]);
            }
        }
    }

    public function Validate(ModelBase $model = null)
    {
        $js =
        'jQuery("#'.$this->formID.'").validate({
            ignore: "",
            rules: {
        ';

        $i=0;
        foreach($this->fields as $field => $rules)
        {
            $j = 0;
            $js .= ($i!=0 ? ", " : "").$field.": {";
            foreach ($rules as $rule)
            {
                $js .= ($j!=0 ? ", " : "").$rule;
                $j++;
            }
            $js .= "}
            ";
            $i++;
        }
        $js .= '
            }';

        if(count($this->messages) > 0)
        {
            $js .= ",
            messages: {";
            $i=0;
            foreach($this->messages as $field => $rules)
            {
                $j = 0;
                $js .= ($i!=0 ? ", " : "").$field.": {";
                foreach($rules as $rule => $msg)
                {
                    $js.= ($j!=0 ? ", " : "")."{$rule}: '{$msg}'";
                    $j++;
                }
                $js .= "}
                ";
                $i++;
            }
            $js .= '
            }';
        }

        $js .= '
            ,errorElement: "span",
            errorPlacement: function(error, element) {
            jQuery(element).next("span").html(error);
            }
        });';

        return $js;
    }

    public function InjectMessage($field, $rule, $msg)
    {
        $this->messages[$field][$rule] = $msg;
    }

    public function ValidateRule($field, $rule, $params = [])
    {
        switch($rule)
        {
            case 'required': $this->fields[$field][$rule] = $this->required(); break;
            case 'minlength': $this->fields[$field][$rule] = $this->minlength($params[0]); break;
            case 'maxlength': $this->fields[$field][$rule] = $this->maxlength($params[0]); break;
            case 'rangelength': $this->fields[$field][$rule] = $this->rangelength($params[0], $params[1]); break;
            case 'min': $this->fields[$field][$rule] = $this->min($params[0]); break;
            case 'max': $this->fields[$field][$rule] = $this->max($params[0]); break;
            case 'range': $this->fields[$field][$rule] = $this->range($params[0], $params[1]); break;
            case 'email': $this->fields[$field][$rule] = $this->email(); break;
            case 'url': $this->fields[$field][$rule] = $this->url(); break;
            case 'date': $this->fields[$field][$rule] = $this->date(); break;
            case 'dateISO': $this->fields[$field][$rule] = $this->dateISO(); break;
            case 'number': $this->fields[$field][$rule] = $this->number(); break;
            case 'digits': $this->fields[$field][$rule] = $this->digits(); break;
            case 'equalTo': $this->fields[$field][$rule] = $this->equalTo($params[0]); break;
            case 'alphanumeric': $this->fields[$field][$rule] = $this->alphanumeric(); break;
            case 'creditcard': $this->fields[$field][$rule] = $this->creditcard(); break;
            case 'integer': $this->fields[$field][$rule] = $this->integer(); break;
            case 'ipv4': $this->fields[$field][$rule] = $this->ipv4(); break;
            case 'ipv6': $this->fields[$field][$rule] = $this->ipv6(); break;
            case 'lettersonly': $this->fields[$field][$rule] = $this->lettersonly(); break;
            case 'letterswithbasicpunc': $this->fields[$field][$rule] = $this->letterswithbasicpunc(); break;
            case 'regex': $this->fields[$field][$rule] = $this->regex($params[0]); break;
            case 'extension': $this->fields[$field][$rule] = $this->extension($params[0]); break;
            case 'additional': $this->fields[$field][$params[0]] = $params[1]; break;
        }
    }

    public function ValidateForm($formID)
    {
        $this->formID = $formID;
    }

    protected function required($val = null) { return "required: true"; }
    protected function minlength($min, $val = null) { return "minlength: {$min}"; }
    protected function maxlength($max, $val = null) { return "maxlength: {$max}"; }
    protected function rangelength($min, $max, $val = null) { return "rangelength: [{$min}, {$max}]"; }
    protected function min($min, $val = null) { return "min: {$min}"; }
    protected function max($max, $val = null) { return "max: {$max}"; }
    protected function range($min, $max, $val = null) { return "range: [{$min}, {$max}]"; }
    protected function email($val = null) { return "email: true"; }
    protected function url($val = null) { return "url: true"; }
    protected function date($val = null) { return "date: true"; }
    protected function dateISO($val = null) { return "dateISO: true"; }
    protected function number($val = null) { return "number: true"; }
    protected function digits($val = null) { return "digits: true"; }
    protected function equalTo($field, $val = null) { return "equalTo: '#{$field}'"; }
    protected function alphanumeric($val = null) { return "alphanumeric: true"; }
    protected function creditcard($val = null) { return "creditcard: true"; }
    protected function integer($val = null) { return "integer: true"; }
    protected function ipv4($val = null) { return "ipv4: true"; }
    protected function ipv6($val = null) { return "ipv6: true"; }
    protected function lettersonly($val = null) { return "lettersonly: true"; }
    protected function letterswithbasicpunc($val = null) { return "letterswithbasicpunc: true"; }
    protected function regex($regex, $val = null) { return "regex: {$regex}"; }
    protected function extension($ext, $val = null) { return "extension: '{$ext}'"; }
}
?>