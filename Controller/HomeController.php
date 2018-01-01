<?php
namespace Controller;
use ASPHP\Core\Controller\ControllerBase;

/**
 * @version 1.0
 * @author Vigan
 */
class HomeController extends ControllerBase
{
    /**
	 * @invoke HttpHandler::Http('POST', 'GET')
     */
    public function Index()
    {
        $this->view->Render('Home/index');
    }

    function ActionDispacher()
    {
        parent::ActionDispacher();
    }

    function DefaultAction()
    {
        $this->InvokeAttributes("Index");
        $this->Index();
    }
}