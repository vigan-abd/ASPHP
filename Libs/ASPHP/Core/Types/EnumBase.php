<?php
namespace ASPHP\Core\Types;

/**
 * @requires interface \ASPHP\Core\Types\IEnum
 * @version 1.0
 * @author Vigan
 */
abstract class EnumBase implements IEnum
{
    private function __construct()
    {
        //Disable instantiation
	}

    /**
     * @param string $member 
     */
    public static function GetAsString($member)
    {
    }

    /**
     * @param integer $member 
     */
    public static function GetFromString($member)
    {
    }
}