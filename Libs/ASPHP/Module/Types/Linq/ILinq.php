<?php
namespace ASPHP\Module\Types\Linq;

interface ILinq
{
    function ToList();
    function ToArray();
    function ToDictionary($keySelector, $elementSelector);
    function AsEnumerable();
}
?>