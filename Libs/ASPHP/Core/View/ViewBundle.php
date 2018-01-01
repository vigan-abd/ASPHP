<?php
namespace ASPHP\Core\View;

class ViewBundle
{
    /**
     * @var ScriptBundle
     */
    protected $script;

    /**
     * @var StyleBundle
     */
    protected $style;

    /**
     * @return ScriptBundle
     */
    public function Script()
    {
        return $this->script;
    }

    /**
     * @return StyleBundle
     */
    public function Style()
    {
        return $this->style;
    }

    public function __construct()
    {
        $this->script = new ScriptBundle();
        $this->style = new StyleBundle();
    }

    public function Render()
    {
        $this->style->Render();
        $this->script->Render();
    }
}

?>