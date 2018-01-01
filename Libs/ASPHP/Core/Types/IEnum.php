<?php
namespace ASPHP\Core\Types;
define('ENUM_CASE_UPPER', 0);
define('ENUM_CASE_LOWER', 1);

/**
 * IEnum base interface
 * @version 1.0
 * @author Vigan
 */
interface IEnum
{
    /**
     * Enter the number of element
     * @param int $member
     * @return string
     */
    static function GetAsString($member);

    /**
     * Enter the name of element
     * @param string $member
     * @return int
     */
    static function GetFromString($member);
}
