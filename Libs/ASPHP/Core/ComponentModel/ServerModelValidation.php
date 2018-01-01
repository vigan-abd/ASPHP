<?php
namespace ASPHP\Core\ComponentModel;
use \ASPHP\Core\Attribute\AttributeReader;
use \ASPHP\Core\Globalization\EnumLanguage;
use \ASPHP\Core\Globalization\CultureInfo;
use \ASPHP\Core\Types\ModelBase;

/**
 * @requires class \ASPHP\Core\ComponentModel\ModelValidationBase
 * @requires class \ASPHP\Core\Attribute\AttributeReader
 * @requires class \ASPHP\Core\Globalization\EnumLanguage
 * @requires class \ASPHP\Core\Globalization\CultureInfo
 * @version 1.0
 * @author Vigan
 */
class ServerModelValidation extends ModelValidationBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function SetModel(ModelBase $model)
    {
        $this->model = $model;
        parent::SetModel($model);
    }

    protected $model = null;

    public function Validate(ModelBase $model = null)
    {
        if(isset($model))
        {
            $this->SetModel($model);
        }
        $props = $this->model->ListProperties();
        $length = count($props);
        for ($i=0; $i < $length; $i++)
        {
            $this->ValidateProperty($props[$i]);
        }
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
                    $this->ValidateRule($this->model->{$prop}, $attr[$i]['key'], explode(',', $attr[$i]['val']), $prop); break;
                default:
                    $this->ValidateRule($this->model->{$prop}, $attr[$i]['key'], [$attr[$i]['val']], $prop);
            }
        }
    }

    public function ValidateRule($field, $rule, $params = [], $prop = "")
    {
        switch($rule)
        {
            case 'required': $this->required($field, $prop); break;
            case 'minlength': $this->minlength($params[0], $field, $prop); break;
            case 'maxlength': $this->maxlength($params[0], $field, $prop); break;
            case 'rangelength': $this->rangelength($params[0], $params[1], $field, $prop); break;
            case 'min': $this->min($params[0], $field, $prop); break;
            case 'max': $this->max($params[0], $field, $prop); break;
            case 'range': $this->range($params[0], $params[1], $field, $prop); break;
            case 'email': $this->email($field, $prop); break;
            case 'url': $this->url($field, $prop); break;
            case 'date': $this->date($field, $prop); break;
            case 'dateISO': $this->dateISO($field, $prop); break;
            case 'number': $this->number($field, $prop); break;
            case 'digits': $this->digits($field, $prop); break;
            case 'equalTo': $this->equalTo(($this->model->HasProperty($params[0]) ? $this->model->{$params[0]} : $params[0]), $field, $prop); break;
            case 'alphanumeric': $this->alphanumeric($field, $prop); break;
            case 'creditcard': $this->creditcard($field, $prop); break;
            case 'integer': $this->integer($field, $prop); break;
            case 'ipv4': $this->ipv4($field, $prop); break;
            case 'ipv6': $this->ipv6($field, $prop); break;
            case 'lettersonly': $this->lettersonly($field, $prop); break;
            case 'letterswithbasicpunc': $this->letterswithbasicpunc($field, $prop); break;
            case 'regex': $this->regex($params[0], $field, $prop); break;
            case 'extension': $this->extension($params[0], $field, $prop); break;
        }
    }

    public function InjectMessage($rule, $msg)
    {
        $this->messages[CultureInfo::GetLanguage()][$rule] = $msg;
    }

    protected function required($val = null, $param = "")
    {
        if(!isset($val) || empty($val))
        {
            throw new \Exception($param." >>> ".$this->messages[CultureInfo::GetLanguage()]['required']);
        }
    }

	protected function minlength($min, $val = null, $param = "")
    {
        if(strlen($val) < $min)
        {
            throw new \Exception(sprintf($param." >>> ".$this->messages[CultureInfo::GetLanguage()]['minlength'], $min));
        }
    }

    protected function maxlength($max, $val = null, $param = "")
    {
        if(strlen($val) > $max)
        {
            throw new \Exception(sprintf($param." >>> ".$this->messages[CultureInfo::GetLanguage()]['maxlength'], $max));
        }
    }

    protected function rangelength($min, $max, $val = null, $param = "")
    {
        $length = strlen($val);
        if($length < $min || $length > $max)
        {
            throw new \Exception(sprintf($param." >>> ".$this->messages[CultureInfo::GetLanguage()]['rangelength'], $min, $max));
        }
    }

    protected function min($min, $val = null, $param = "")
    {
        if($val < $min)
        {
            throw new \Exception(sprintf($param." >>> ".$this->messages[CultureInfo::GetLanguage()]['min'], $min));
        }
    }

    protected function max($max, $val = null, $param = "")
    {
        if($val > $max)
        {
            throw new \Exception(sprintf($param." >>> ".$this->messages[CultureInfo::GetLanguage()]['max'], $max));
        }
    }

    protected function range($min, $max, $val = null, $param = "")
    {
        if($val > $max || $val < $min)
        {
            throw new \Exception(sprintf($param." >>> ".$this->messages[CultureInfo::GetLanguage()]['range'], $min, $max));
        }
    }

    protected function email($val = null, $param = "")
    {
        if(!preg_match('/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/', $val))
        {
            throw new \Exception($param." >>> ".$this->messages[CultureInfo::GetLanguage()]['email']);
        }
    }

    protected function url($val = null, $param = "")
    {
		if(!preg_match('/^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z0-9]-*)*[a-z0-9]+)(?:\.(?:[a-z0-9]-*)*[a-z0-9]+)*(?:\.(?:[a-z]{2,})).?)(?::\d{2,5})?(?:[\/?#]\S*)?$/i', $val))
		{
            throw new \Exception($param." >>> ".$this->messages[CultureInfo::GetLanguage()]['url']);
		}
    }

    protected function date($val = null, $param = "")
    {
        try
        {
            $date = new \DateTime($val);
        }
        catch (\Exception $ex)
        {
            throw new \Exception($param." >>> ".$this->messages[CultureInfo::GetLanguage()]['date']);
        }
    }

    protected function dateISO($val = null, $param = "")
    {
        if(!preg_match('/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/', $val))
        {
            throw new \Exception($param." >>> ".$this->messages[CultureInfo::GetLanguage()]['dateISO']);
        }
    }

    protected function number($val = null, $param = "")
    {
        if(!preg_match('/^(?:-?\d+|-?\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/', $val))
        {
            throw new \Exception($param." >>> ".$this->messages[CultureInfo::GetLanguage()]['number']);
        }
    }

    protected function digits($val = null, $param = "")
    {
        if(!preg_match('/^\d+$/', $val))
        {
            throw new \Exception($param." >>> ".$this->messages[CultureInfo::GetLanguage()]['digits']);
        }
    }

    protected function equalTo($field, $val = null, $param = "")
    {
        if($field != $val)
        {
            throw new \Exception($param." >>> ".$this->messages[CultureInfo::GetLanguage()]['equalTo']);
        }
    }

    protected function alphanumeric($val = null, $param = "")
    {
        if(!preg_match('/^\w+$/i', $val))
        {
            throw new \Exception($param." >>> ".$this->messages[CultureInfo::GetLanguage()]['alphanumeric']);
        }
    }

    protected function creditcard($val = null, $param = "")
    {
        if(preg_match('/[^0-9 \-]+/', $val))
        {
            throw new \Exception($param." >>> ".$this->messages[CultureInfo::GetLanguage()]['creditcard']);
        }
        $val = preg_replace('/\D/', "", $val);
        $length = strlen($val);
        if($length > 19 || $length <13)
        {
            throw new \Exception($param." >>> ".$this->messages[CultureInfo::GetLanguage()]['creditcard']);
        }

	    $nCheck = 0;
		$bEven = false;
	    for ( $n = $length - 1; $n >= 0; $n-- )
        {
		    $nDigit = intval($val[$n]);
		    if ($bEven)
            {
			    if(($nDigit *= 2) > 9 )
                {
				    $nDigit -= 9;
                }
			}
		    $nCheck += $nDigit;
		    $bEven = !$bEven;
		}

        if(!($nCheck % 10 ) == 0)
        {
            throw new \Exception($param." >>> ".$this->messages[CultureInfo::GetLanguage()]['creditcard']);
        }
    }

    protected function integer($val = null, $param = "")
    {
        if(!preg_match('/^-?\d+$/', $val))
        {
            throw new \Exception($param." >>> ".$this->messages[CultureInfo::GetLanguage()]['integer']);
        }
    }

    protected function ipv4($val = null, $param = "")
    {
        if(!preg_match('/^(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)$/i', $val))
        {
            throw new \Exception($param." >>> ".$this->messages[CultureInfo::GetLanguage()]['ipv4']);
        }
    }

    protected function ipv6($val = null, $param = "")
    {
        if(!preg_match('/^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$/i', $val))
        {
            throw new \Exception($param." >>> ".$this->messages[CultureInfo::GetLanguage()]['ipv6']);
        }
    }

    protected function lettersonly($val = null, $param = "")
    {
        if(!preg_match('/^[a-z]+$/i', $val))
        {
            throw new \Exception($param." >>> ".$this->messages[CultureInfo::GetLanguage()]['lettersonly']);
        }
    }

    protected function letterswithbasicpunc($val = null, $param = "")
    {
        if(!preg_match('/^[a-z\-.,()\'"\s]+$/i', $val))
        {
            throw new \Exception($param." >>> ".$this->messages[CultureInfo::GetLanguage()]['letterswithbasicpunc']);
        }
    }

    protected function regex($regex, $val = null, $param = "")
    {
        if(!preg_match($regex, $val))
        {
            throw new \Exception($param." >>> ".$this->messages[CultureInfo::GetLanguage()]['regex']);
        }
    }

    protected function extension($ext, $val = null, $param = "")
    {
        $ext = explode('|', trim($ext, '|'));
        $val = pathinfo($val, PATHINFO_EXTENSION);
        if(!in_array($val, $ext))
        {
            throw new \Exception($param." >>> ".$this->messages[CultureInfo::GetLanguage()]['extension']);
        }
    }

    protected $messages =
    [
        EnumLanguage::EN => ["required" => "This field is required.", "minlength" => "Please enter at least %s characters.", "maxlength" => "Please enter no more than %s characters.", "rangelength" => "Please enter a value between %s and %s characters long.", "min" => "Please enter a value greater than or equal to %s.", "max" => "Please enter a value less than or equal to %s.", "range" => "Please enter a value between %s and %s.", "email" => "Please enter a valid email address.", "url" => "Please enter a valid URL.", "date" => "Please enter a valid date.", "dateISO" => "Please enter a valid date ( ISO ).", "number" => "Please enter a valid number.", "digits" => "Please enter only digits.", "equalTo" => "Please enter the same value again.", "alphanumeric" => "Letters, numbers, and underscores only please", "creditcard" => "Please enter a valid credit card number.", "integer" => "A positive or negative non-decimal number please", "ipv4" => "Please enter a valid IP v4 address.", "ipv6" =>  "Please enter a valid IP v6 address.", "lettersonly" => "Letters only please", "letterswithbasicpunc" => "Letters or punctuation only please", "regex" =>  "Please check your input.", "extension" => "Please enter a value with a valid extension."],
        EnumLanguage::AL => ["required" => "Fusha duhet te plotesohet.", "minlength" => "Numri minimal i karaktereve te lejuara eshte %s.", "maxlength" => "Numri maksimal i karaktereve te lejuara eshte %s.", "rangelength" => "Numri i karaktereve duhet te jete ne mes %s dhe %s.", "min" => "Vlera maksimale e lejuar eshte %s.", "max" => "Vlera minimale e lejuar eshte %s.", "range" => "Ju lutemi jepni vlere ne mes te rangut %s - %s.", "email" => "Ju lutemi jepni email adres valide.", "url" => "Ju lutemi jepni web adrese (URL) valide.", "date" => "Ju lutemi jepni date valide .", "dateISO" => "Ju lutemi jepni date valide (Formati ISO).", "number" => "Ju lutemi jepni vlere numerike.", "digits" => "Ju lutemi jepni vlere numerike (pa pike)", "equalTo" => "Ju lutemi jepni perseri vleren.", "alphanumeric" => "Letters, numbers, and underscores only please", "creditcard" => "Ju lutemi jepni kredit kartele valide.", "integer" => "Ju lutemi jepni vlere numerike (pa pike)", "ipv4" => "Ju lutemi jepni IP v4 adrese valide.", "ipv6" =>  "Ju lutemi jepni IP v6 adrese valide.", "lettersonly" => "Ju lutemi jepni vetem shkronja.", "letterswithbasicpunc" => "Ju lutemi jepni vetem shkronja dhe shenja te pikesimit.", "regex" =>  "Ju lutemi shiqoni input e juaj edhe nje here.", "extension" => "Ju lutemi jepni ekstension valid."],
        EnumLanguage::AR => [],
        EnumLanguage::BG => [],
        EnumLanguage::BN_BD => [],
        EnumLanguage::CA => [],
        EnumLanguage::CS => [],
        EnumLanguage::DA => [],
        EnumLanguage::DE => [],
        EnumLanguage::EL => [],
        EnumLanguage::ES => [],
        EnumLanguage::ET => [],
        EnumLanguage::EU => [],
        EnumLanguage::FA => [],
        EnumLanguage::FR => [],
        EnumLanguage::GE => [],
        EnumLanguage::GL => [],
        EnumLanguage::HE => [],
        EnumLanguage::HR => [],
        EnumLanguage::HU => [],
        EnumLanguage::HY => [],
        EnumLanguage::ID => [],
        EnumLanguage::IS => [],
        EnumLanguage::IT => [],
        EnumLanguage::JA => [],
        EnumLanguage::KA => [],
        EnumLanguage::KK => [],
        EnumLanguage::KO => [],
        EnumLanguage::LT => [],
        EnumLanguage::LV => [],
        EnumLanguage::MK => [],
        EnumLanguage::MY => [],
        EnumLanguage::NL => [],
        EnumLanguage::NO => [],
        EnumLanguage::PL => [],
        EnumLanguage::PT_BR => [],
        EnumLanguage::PT_OT => [],
        EnumLanguage::RO => [],
        EnumLanguage::RU => [],
        EnumLanguage::SI => [],
        EnumLanguage::SK => [],
        EnumLanguage::SL => [],
        EnumLanguage::SR => [],
        EnumLanguage::SV => [],
        EnumLanguage::TH => [],
        EnumLanguage::TJ => [],
        EnumLanguage::TR => [],
        EnumLanguage::UK => [],
        EnumLanguage::VI => [],
        EnumLanguage::ZH => [],
    ];
}
?>