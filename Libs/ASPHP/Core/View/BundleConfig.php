<?php
namespace ASPHP\Core\View;

class BundleConfig
{
    /**
     * @var ViewBundle
     */
    protected $header;

    /**
     * @var ViewBundle
     */
    protected $footer;

    /**
     * @return ViewBundle
     */
    public function Header()
    {
        return $this->header;
    }

    /**
     * @return ViewBundle
     */
    public function Footer()
    {
        return $this->footer;
    }

    public function __construct()
    {
        $this->header = new ViewBundle();
        $this->footer = new ViewBundle();
    }
}

?>