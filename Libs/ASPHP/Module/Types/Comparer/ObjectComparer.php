<?php
namespace ASPHP\Module\Types\Comparer;

/**
 * @requires interface \ASPHP\Module\Types\Comparer\IEqualityComparer
 * @version 1.0
 * @author Vigan
 */
class ObjectComparer implements IEqualityComparer
{
    function Equals($var1, $var2)
    {
        return $this->GetHashCode($var1) == $this->GetHashCode($var2);
    }

    function GetHashCode($var)
    {
        return spl_object_hash($var);
    }
}
