<?php
namespace ASPHP\Module\Types\Collection;

final class KeyValuePair
{
    private $key;
    private $value;

    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function Key()
    {
        return $this->key;
    }

    public function Value()
    {
        return $this->value;
    }
}
?>