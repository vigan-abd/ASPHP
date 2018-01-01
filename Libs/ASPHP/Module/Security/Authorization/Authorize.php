<?php
namespace ASPHP\Module\Security\Authorization;
use \ASPHP\Core\Types\StaticClass;
use \ASPHP\Core\ErrorHandler\Error;

/**
 * @requires class \ASPHP\Core\Types\StaticClass
 * @requires class \ASPHP\Core\Types\Error
 * @version 1.0
 * @author Vigan
 */
class Authorize extends StaticClass
{
    public static function Roles()
    {
        $args = func_get_args();
    }

    public static function Users()
    {
        $args = func_get_args();
    }
}