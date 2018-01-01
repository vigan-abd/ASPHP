<?php
namespace ASPHP\Module\Types\Comparer;

/**
 * @version 1.0
 * @author Vigan
 */
interface IEqualityComparer
{
    /**
     * @param mixed $var1
     * @param mixed $var2
     * @return bool
     */
    public function Equals($var1, $var2);
    /**
     * @param mixed $var
     * @return string
     */
    public function GetHashCode($var);
}