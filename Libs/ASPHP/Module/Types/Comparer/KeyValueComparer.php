<?php
namespace ASPHP\Module\Types\Comparer;

/**
 * @requires interface \ASPHP\Module\Types\Comparer\IEqualityComparer
 * @version 1.0
 * @author Vigan
 */
class KeyValueComparer implements IEqualityComparer
{
    function Equals($var1, $var2)
    {
        return $var1->Key() == $var2->Key() && $var1->Value() == $var2->Value();
    }

    function GetHashCode($var)
    {
        return spl_object_hash($var);
    }
}
?>