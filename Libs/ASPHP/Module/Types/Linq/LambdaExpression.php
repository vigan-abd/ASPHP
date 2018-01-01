<?php
namespace ASPHP\Module\Types\Linq;

/**
 * @version 1.0
 * @author Vigan
 */
class LambdaExpression
{
    protected $args;
    protected $body;

    public function DetectLambda($input)
    {
        if(!is_string($input))
            return false;
        if(preg_match('/^\(*((\$[a-zA-Z0-9\_]+)\s*\,*\s*)+\)*\s*=>\s*.+$/', trim($input)))
            return true;
        else
            return false;
    }

    /**
     * @param string $input
     */
    public function ExtractLambda($input)
    {
        $array = explode('=>', trim($input));
        $array[0] = trim(trim($array[0]), ',()');
        $this->args = explode(',', $array[0]);
        $this->body = trim(trim($array[1]), '{}');
		return $this;
    }

    /**
     * @return \Closure
     */
    public function Build()
    {
        $code = "";
        $lamdba = 'function (';
        $length = count($this->args);
        for ($i = 0; $i < $length; $i++)
        {
        	if($i != 0)
                $lamdba .= ",".$this->args[$i];
            else
                $lamdba .= $this->args[$i];
        }
        $lamdba.= ') { '.(preg_match('/return/', $this->body) ? $this->body : 'return '.$this->body.';').' }';
        eval('$code = '.$lamdba.';');
        return $code;
    }
}