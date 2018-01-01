<?php
namespace ASPHP\Core\View;
use \ASPHP\Core\Configuration\Config;

final class StyleBundle extends Bundle
{
    public function Render()
    {
        parent::Render();
        $html = "";
        foreach ($this->queue as $src)
        {
            $html .= '
            <link rel="stylesheet" type="text/css" href="'.Config::Get()["environment"]["directory"]["publicDir"]."/{$src}\" />";
        }
        echo $html;
    }
}

?>