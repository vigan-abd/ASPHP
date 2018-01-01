<?php
namespace Controller;
use ASPHP\Core\Controller\ControllerBase;
use \ASPHP\Core\ErrorHandler\StatusMessage;

/**
 * @version 1.0
 * @author Vigan
 */
class ErrorController extends ControllerBase
{
    public function Index()
    {
        if(count($this->params) < 2)
        {
            StatusMessage::GetInstance()->SetCode(404);
            StatusMessage::GetInstance()->SetMessage("BAD REQUEST");
            StatusMessage::GetInstance()->SetStatusType(StatusMessage::ERROR);
        }
        else if(count($this->params) < 3)
        {
            StatusMessage::GetInstance()->SetCode($this->params[0]);
            StatusMessage::GetInstance()->SetMessage($this->params[1]);
            StatusMessage::GetInstance()->SetStatusType(StatusMessage::ERROR);
        }
        else
        {
            StatusMessage::GetInstance()->SetCode($this->params[0]);
            StatusMessage::GetInstance()->SetMessage($this->params[1]);
            StatusMessage::GetInstance()->SetStatusType($this->params[2]);
        }
        $this->view->Render('Response/response.php', StatusMessage::GetInstance());
    }

    function ActionDispacher()
    {
        parent::ActionDispacher();
    }

    function DefaultAction()
    {
        $this->Index();
    }
}