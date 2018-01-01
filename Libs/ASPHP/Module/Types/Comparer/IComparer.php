<?php
namespace ASPHP\Module\Types\Comparer;

/**
 * @version 1.0
 * @author Vigan
 */
interface IComparer
{
    /**
     * @param mixed $var1
     * @param mixed $var2
     * @return int
     */
    public function Compare($var1, $var2);
}