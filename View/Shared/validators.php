<?php
use \ASPHP\Core\Globalization\CultureInfo;
use \ASPHP\Core\Globalization\EnumLanguage;
$this->view->Bundle()->Footer()->Script()->Add('jval', 'Scripts/jquery.validation/jquery.validate.min.js');
$this->view->Bundle()->Footer()->Script()->Add('jvalAdd', 'Scripts/jquery.validation/additional-methods.min.js');
if(CultureInfo::GetLanguage() != EnumLanguage::EN)
{
    $this->view->Bundle()->Footer()->Script()->Add('jvalAdd', 'Scripts/jquery.validation/localization/messages_'.CultureInfo::GetLanguage().'.min.js');
}
?>