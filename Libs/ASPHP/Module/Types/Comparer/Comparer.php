<?php
namespace ASPHP\Module\Types\Comparer;

/**
 * @requires interface \ASPHP\Module\Types\Comparer\IComparer
 * @version 1.0
 * @author Vigan
 */
abstract class Comparer implements IComparer
{
    /**
     * -1 => $var1 < $var2, 0 => $var1 == $var2, 1 => $var1 > $var2
     * @param mixed $var1
     * @param mixed $var2
     * @return int
     */
    abstract function Compare($var1, $var2);

    /**
     * @return \Closure
     */
    public static function DefaultCompare()
    {
        return function ($var1, $var2)
        {
            if($var1 < $var2)
                return -1;
            else if($var1 == $var2)
                return 0;
            else return 1;
        };
    }
}
