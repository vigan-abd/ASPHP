<?php
namespace ASPHP\Core\View;
use \ASPHP\Core\Configuration\Config;

final class ScriptBundle extends Bundle
{
    public function Render()
    {
        parent::Render();
        $html = "";
        foreach ($this->queue as $src)
        {
            $html .= '
            <script src="'.Config::Get()["environment"]["directory"]["publicDir"]."/{$src}\"></script>";
        }
        echo $html;
    }
}

?>