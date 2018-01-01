<?php
require_once __DIR__.'/Resolver.php';
function __autoload($classname)
{
    \ASPHP\Core\Resolving\Resolver::Resolve($classname);
}
?>