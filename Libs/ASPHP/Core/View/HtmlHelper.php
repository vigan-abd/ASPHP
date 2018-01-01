<?php
namespace ASPHP\Core\View;
use \ASPHP\Core\ComponentModel\ClientModelValidation;
use \ASPHP\Core\Web\CSRF;
use \ASPHP\Core\Net\HttpClient;
use \ASPHP\Core\Routing\Router;
use \ASPHP\Core\Configuration\Config;

class HtmlHelper
{
    public function __construct($model, $view)
    {
        $this->model = $model;
		$this->view = $view;
    }

	/**
	 * @var View
	 */
	protected $view;

    /**
	 * @var mixed
     */
    protected $model;

    /**
	 * @var ClientModelValidation
     */
    protected $val;

    /**
	 * @var bool
     */
    protected $enableVal;

    public function EnableClientValidation()
    {
        $this->enableVal = true;
        $this->val = new ClientModelValidation();
        $this->val->SetModel($this->model);
    }

    /**
     * @param string $controller
     * @param string $action
     * @param array $params
     * @return string
     */
    public function Action($controller, $action, $params = [])
    {
        return HttpClient::GET(Router::BuildRouteString($controller, $action, $params));
    }

    /**
     * @param string $controller
     * @param string $action
     * @param array $params
     */
    public function RenderAction($controller, $action, $params = [])
    {
        echo static::Action($controller, $action, $params);
    }

    /**
     * Renders partial content ~/View/{$view}. Also automatically appends data to $_VIEWBAG
     * @param string $view view name (~/View/ folder is automatically prepended)
     * @param mixed $data
     * @return void
     */
    public function Partial($view, $data = [])
    {
        global $_VIEWBAG;
        foreach($data as $k => $v)
            $_VIEWBAG[$k] = $v;
        require Config::Get()["environment"]["directory"]["~"].'/View/'.$view.(pathinfo($view, PATHINFO_EXTENSION) == "" ? ".php" : "");
    }

    protected function ReadAttributes($htmlAttributes = [])
    {
        $attr = "";
        foreach($htmlAttributes as $k => $v)
            $attr .= $k.'="'.$v.'" ';
        return $attr;
    }

    /**
     * @param string $value
     */
    public function AttributeEncode($value)
    {
        echo htmlspecialchars($value, ENT_QUOTES);
    }

    /**
     * @param string $value
     */
    public function Encode($value)
    {
        echo htmlentities($value, ENT_QUOTES);
    }

    /**
     * @param string $value
     */
    public function Raw($value)
    {
        echo $value;
    }

    public function AntiForgeryToken()
    {
        echo CSRF::GenerateToken();
    }

    /**
     * @param string $linkText
     * @param string $controller
     * @param string $action
     * @param array $params
     * @param array $htmlAttributes
     */
    public function ActionLink($linkText, $controller, $action, $params = [], $htmlAttributes = [])
    {
        $attr = $this->ReadAttributes($htmlAttributes);
        echo '<a href="'.Router::BuildRouteString($controller, $action, $params).'" '.$attr.'>'.$linkText.'</a>';
    }

    public function BeginForm($controller, $action, $formMethod, $params = [], $htmlAttributes = ["id" => "frm1"])
    {
        $attr = $this->ReadAttributes($htmlAttributes);
        echo '<form method="'.$formMethod.'" action="'.Router::BuildRouteString($controller, $action, $params).'" '.$attr.'>';
        if($this->enableVal)
        {
            $this->val->ValidateForm($htmlAttributes["id"]);
        }
    }

    public function EndForm()
    {
        echo '</form>';
    }

    public function EditorFor($property, $htmlAttributes = ["class" => "form-control"])
    {
        $type = $this->val->ReadType($property);
        switch($type)
        {
            case 'DateTime': $this->DateTimeFor($property, $htmlAttributes); break;
            case 'int':
            case 'float':
            case 'decimal':
            case 'integer':
            case 'number': $this->NumberFor($property, $htmlAttributes); break;
            case 'bool': $this->CheckBoxFor($property, "true", $htmlAttributes); break;
            default: $this->TextBoxFor($property, $htmlAttributes); break;
        }
    }

    public function DateTime($name, $value = "", $htmlAttributes = ["class" => "form-control"])
    {
        $attr = $this->ReadAttributes($htmlAttributes);
        echo '<input name="'.$name.'" type="datetime" value="'.$value.'" '.$attr.'/>';
    }

    public function DateTimeFor($property, $htmlAttributes = ["class" => "form-control"])
    {
        $attr = $this->ReadAttributes($htmlAttributes);
        echo '<input type="datetime"'.(empty($htmlAttributes['id']) ? ' id="'.$property.'" ' : "").
            (empty($htmlAttributes['name']) ? ' name="'.$property.'" ' : " ")
            .((!empty($this->model->{$property}) && empty($htmlAttributes['value'])) ? ' value="'.$this->model->{$property}.'" ' : '').
            $attr.' />';
        if($this->enableVal)
            $this->val->ValidateProperty($property);
    }

    public function Number($name, $value = "", $htmlAttributes = ["class" => "form-control"])
    {
        $attr = $this->ReadAttributes($htmlAttributes);
        echo '<input name="'.$name.'" type="number" value="'.$value.'" '.$attr.'/>';
    }

    public function NumberFor($property, $htmlAttributes = ["class" => "form-control"])
    {
        $attr = $this->ReadAttributes($htmlAttributes);
        echo '<input type="number"'.(empty($htmlAttributes['id']) ? ' id="'.$property.'" ' : "").
            (empty($htmlAttributes['name']) ? ' name="'.$property.'" ' : " ")
            .((!empty($this->model->{$property}) && empty($htmlAttributes['value'])) ? ' value="'.$this->model->{$property}.'" ' : '').
            $attr.' />';
        if($this->enableVal)
            $this->val->ValidateProperty($property);
    }

    public function CheckBox($name, $value, $isChecked, $htmlAttributes = ["class" => "form-control"])
    {
        $attr = $this->ReadAttributes($htmlAttributes);
        echo '<input '.($isChecked ? 'checked="checked"' : '').' name="'.$name.'" type="checkbox" value="'.$value.'" '.$attr.'/>';
    }

    public function CheckBoxFor($property, $value = "true", $htmlAttributes = ["class" => "form-control"])
    {
        $attr = $this->ReadAttributes($htmlAttributes);
        echo '<input '.((!empty($this->model->{$property}) && $this->model->{$property}) ? 'checked="checked"' : '')
        .' type="checkbox"'.(empty($htmlAttributes['id']) ? ' id="'.$property.'" ' : "").
            (empty($htmlAttributes['name']) ? ' name="'.$property.'" ' : " ")
            .'value="'.$value.'" '.$attr.'/>';
        if($this->enableVal)
            $this->val->ValidateProperty($property);
    }

    public function Hidden($name, $value = "", $htmlAttributes = ["class" => "form-control"])
    {
        $attr = $this->ReadAttributes($htmlAttributes);
        echo '<input name="'.$name.'" type="hidden" value="'.$value.'" '.$attr.'/>';
    }

    public function HiddenFor($property, $htmlAttributes = ["class" => "form-control"])
    {
        $attr = $this->ReadAttributes($htmlAttributes);
        echo '<input type="hidden"'.(empty($htmlAttributes['id']) ? ' id="'.$property.'" ' : "").
            (empty($htmlAttributes['name']) ? ' name="'.$property.'" ' : " ")
            .((!empty($this->model->{$property}) && empty($htmlAttributes['value'])) ? ' value="'.$this->model->{$property}.'" ' : '').
            $attr.' />';
        if($this->enableVal)
            $this->val->ValidateProperty($property);
    }

    public function Label($field, $text, $htmlAttributes = ["class" => "col-md-2 control-label"])
    {
        $attr = $this->ReadAttributes($htmlAttributes);
        echo '<label for="'.$field.'" '.$attr.'>'.$text.'</label>';
    }

    public function LabelFor($property, $htmlAttributes = ["class" => "col-md-2 control-label"])
    {
        $txt = $property;
        $mAttr = $this->val->ReadAttributes($property);
        $length = count($mAttr);
        for($i=0; $i<$length; $i++)
        {
            if($mAttr[$i]['key'] == 'display')
            { $txt = $mAttr[$i]['val']; break; }
        }
        $attr = $this->ReadAttributes($htmlAttributes);
        echo '<label for="'.$property.'" '.$attr.'>'.$txt.'</label>';
    }

    public function DropDownList($name, \ASPHP\Core\View\SelectList $collection, $htmlAttributes = ["class" => "form-control"])
    {
        $attr = $this->ReadAttributes($htmlAttributes);
        $html = '
        <select name="'.$name.'" '.$attr.'>';
        $items = $collection->Items();
        $length = count($items);
        for($i=0; $i<$length; $i++)
        {
            if($collection->HasGroups())
            {
                if($items[$i]->IsEmpty())
                    continue;
                $length2 = count($items[$i]->Items());
                $listItems = $items[$i]->Items();
                $html .= '<optgroup label="'.$items[$i]->text.'" '.($items[$i]->disabled ? 'disabled="disabled"' : '').'>';
                for($j=0; $j<$length2; $j++)
                {
                    $html .= '<option value="'.$listItems[$j]->value.'" '.($listItems[$j]->disabled ? 'disabled="disabled"' : '').
                     ($listItems[$j]->selected ? ' selected="selected"' : '').'>'.$listItems[$j]->text.'</option>';
                }
                $html .= '</optgroup>';
            }
            else
            {
                $length2 = count($items[$i]->Items());
                $listItems = $items[$i]->Items();
                for($j=0; $j<$length2; $j++)
                {
                    $html .= '<option value="'.$listItems[$j]->value.'" '.($listItems[$j]->disabled ? 'disabled="disabled"' : '').
                     ($listItems[$j]->selected ? ' selected="selected"' : '').'>'.$listItems[$j]->text.'</option>';
                }
            }
        }

        $html.='
        </select>';
        echo $html;
    }

    public function DropDownListFor($property, \ASPHP\Core\View\SelectList $collection, $htmlAttributes = ["class" => "form-control"])
    {
        $attr = $this->ReadAttributes($htmlAttributes);
        $html = '
        <select '.(empty($htmlAttributes['id']) ? ' id="'.$property.'" ' : "").
            (empty($htmlAttributes['name']) ? ' name="'.$property.'" ' : " ").' '.$attr.'>';
        $items = $collection->Items();
        $length = count($items);
        for($i=0; $i<$length; $i++)
        {
            if($collection->HasGroups())
            {
                if($items[$i]->IsEmpty())
                    continue;
                $length2 = count($items[$i]->Items());
                $listItems = $items[$i]->Items();
                $html .= '<optgroup label="'.$items[$i]->text.'" '.($items[$i]->disabled ? 'disabled="disabled"' : '').'>';
                for($j=0; $j<$length2; $j++)
                {
                    $html .= '<option value="'.$listItems[$j]->value.'" '.($listItems[$j]->disabled ? 'disabled="disabled"' : '').
                     ($listItems[$j]->selected ? ' selected="selected"' : '').'>'.$listItems[$j]->text.'</option>';
                }
                $html .= '</optgroup>';
            }
            else
            {
                $length2 = count($items[$i]->Items());
                $listItems = $items[$i]->Items();
                for($j=0; $j<$length2; $j++)
                {
                    $html .= '<option value="'.$listItems[$j]->value.'" '.($listItems[$j]->disabled ? 'disabled="disabled"' : '').
                     ($listItems[$j]->selected ? ' selected="selected"' : '').'>'.$listItems[$j]->text.'</option>';
                }
            }
        }

        $html.='
        </select>';
        echo $html;
        if($this->enableVal)
            $this->val->ValidateProperty($property);
    }

    public function ListBox($name, \ASPHP\Core\View\SelectList $collection, $htmlAttributes = ["class" => "form-control"])
    {
        $this->DropDownListFor($name, $collection, array_merge($htmlAttributes, ["multiple" => "multiple"]));
    }

    public function ListBoxFor($property, \ASPHP\Core\View\SelectList $collection, $htmlAttributes = ["class" => "form-control"])
    {
        $this->DropDownListFor($property, $collection, array_merge($htmlAttributes, ["multiple" => "multiple"]));
    }

    public function Password($name, $value = "", $htmlAttributes = ["class" => "form-control"])
    {
        $attr = $this->ReadAttributes($htmlAttributes);
        echo '<input name="'.$name.'" type="password" value="'.$value.'" '.$attr.'/>';
    }

    public function PasswordFor($property, $htmlAttributes = ["class" => "form-control"])
    {
        $attr = $this->ReadAttributes($htmlAttributes);
        echo '<input type="password"'.(empty($htmlAttributes['id']) ? ' id="'.$property.'" ' : "").
            (empty($htmlAttributes['name']) ? ' name="'.$property.'" ' : " ")
            .((!empty($this->model->{$property}) && empty($htmlAttributes['value'])) ? ' value="'.$this->model->{$property}.'" ' : '').
            $attr.' />';
        if($this->enableVal)
            $this->val->ValidateProperty($property);
    }

    public function RadioButton($name, $value, $isChecked, $htmlAttributes = ["class" => "form-control"])
    {
        $attr = $this->ReadAttributes($htmlAttributes);
        echo '<input '.($isChecked ? 'checked="checked"' : '').' name="'.$name.'" type="radio" value="'.$value.'" '.$attr.'/>';
    }

    public function RadioButtonFor($property, $value, $htmlAttributes = ["class" => "form-control"])
    {
        $attr = $this->ReadAttributes($htmlAttributes);
        echo '<input checked="checked" type="radio"'.(empty($htmlAttributes['id']) ? ' id="'.$property.'" ' : "").
            (empty($htmlAttributes['name']) ? ' name="'.$property.'" ' : " ")
            .'value="'.$value.'" '.$attr.'/>';
        if($this->enableVal)
            $this->val->ValidateProperty($property);
    }

    public function TextArea($name, $value = "", $htmlAttributes = ["class" => "form-control"])
    {
        $attr = $this->ReadAttributes($htmlAttributes);
        echo '<textarea name="'.$name.'" '.$attr.'>'.$value.'</textarea>';
    }

    public function TextAreaFor($property, $htmlAttributes = ["class" => "form-control"])
    {
        $attr = $this->ReadAttributes($htmlAttributes);
        echo '<textarea'.(empty($htmlAttributes['id']) ? ' id="'.$property.'" ' : "").
            (empty($htmlAttributes['name']) ? ' name="'.$property.'" ' : " ").$attr.
        '>'.(!empty($this->model->{$property}) ? $this->model->{$property} : '').'</textarea>';
        if($this->enableVal)
            $this->val->ValidateProperty($property);
    }

    public function TextBox($name, $value = "", $htmlAttributes = ["class" => "form-control"])
    {
        $attr = $this->ReadAttributes($htmlAttributes);
        echo '<input name="'.$name.'" type="text" value="'.$value.'" '.$attr.'/>';
    }

    public function TextBoxFor($property, $htmlAttributes = ["class" => "form-control"])
    {
        $attr = $this->ReadAttributes($htmlAttributes);
        echo '<input type="text"'.(empty($htmlAttributes['id']) ? ' id="'.$property.'" ' : "").
            (empty($htmlAttributes['name']) ? ' name="'.$property.'" ' : " ")
            .((!empty($this->model->{$property}) && empty($htmlAttributes['value'])) ? ' value="'.$this->model->{$property}.'" ' : '').
            $attr.' />';
        if($this->enableVal)
            $this->val->ValidateProperty($property);
    }

    public function ValidationMessageFor($property, $htmlAttributes = ["class" => "field-validation-valid text-danger"])
    {
        $attr = $this->ReadAttributes($htmlAttributes);
        echo '<span '.$attr.' data-valmsg-for="'.$property.'" data-valmsg-replace="true"></span>';
    }

    public function AddValidationRule($property, $rule, $body)
    {
        $this->val->ValidateRule($property, 'additional', [$rule, $body]);
    }

    public function AddValidationMessage($property, $rule, $msg)
    {
        $this->val->InjectMessage($property, $rule, $msg);
    }

    public function Validate()
    {
        echo '
        <script>
        if (window.addEventListener) {
	        window.addEventListener("load", function(){
                '.$this->val->Validate().'
	        }, false);
        }
        else if (window.attachEvent) {// Microsoft
	        window.attachEvent("onload", function(){
                '.$this->val->Validate().'
	        });
        }
        </script>';
    }
}