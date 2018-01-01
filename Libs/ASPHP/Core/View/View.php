<?php
namespace ASPHP\Core\View;
use \ASPHP\Core\Configuration\Config;

class View
{
    /**
     * @var mixed
     */
    public $model;

    /**
     * @var HtmlHelper
     */
    public $htmlHelper;

    /**
     * Tells whenever to load the view as fullpage or as partial
     * @var string
     */
    public $isPartial = false;

    /**
     * Style/Script Sources
     * @var BundleConfig
     */
    protected $bundle;

    /**
     * Returns BundleConfig
     * @return BundleConfig
     */
    public function Bundle()
    {
        return $this->bundle;
    }

    /**
     * Footer part source
     * @var string
     */
    public $footerSrc = "Shared/footer.php";

    /**
     * Header part source
     * @var string
     */
    public $headerSrc = "Shared/header.php";

    public function __construct()
    {
        $this->bundle = new BundleConfig();
    }

    /**
     * Renders the content ~/View/{$view}
     * @param string $view view name (~/View/ folder is automatically prepended)
     * @param mixed $model
     * @return void
     */
    public function Render($view, $model = null)
    {
        global $_VIEWBAG;
        $this->model = $model;
        $this->htmlHelper = new HtmlHelper($this->model, $this);
        if($this->isPartial)
        {
            require_once Config::Get()["environment"]["directory"]["~"].'/View/'.$view.(pathinfo($view, PATHINFO_EXTENSION) == "" ? ".php" : "");
        }
        else
        {
            require_once Config::Get()["environment"]["directory"]["~"].'/View/'.$this->headerSrc;
            require_once Config::Get()["environment"]["directory"]["~"].'/View/'.$view.(pathinfo($view, PATHINFO_EXTENSION) == "" ? ".php" : "");
            require_once Config::Get()["environment"]["directory"]["~"].'/View/'.$this->footerSrc;
        }
    }
}
?>