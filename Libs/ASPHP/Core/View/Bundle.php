<?php
namespace ASPHP\Core\View;

abstract class Bundle
{
    protected $queue = [];

    protected $count = 0;

    public function Add($name, $src)
    {
        $this->queue[$name] = $src;
        $this->count++;
    }

    public function Remove($name)
    {
        $this->queue[$name] = "";
        if($this->count > 0)
            $this->count--;
    }

    public function Clear()
    {
        $this->queue = [];
        $this->count = 0;
    }

    public function Render()
    {
        if($this->count == 0)
            return;
    }
}

?>