<?php
namespace ASPHP\Core\View;

class SelectListGroup extends \ASPHP\Core\Types\ModelBase
{
    /**
     * @var bool
     */
    public $disabled;
    /**
     * @var string
     */
    public $text;

    protected $isEmpty = true;

    public function IsEmpty()
    {
        return $this->isEmpty;
    }

    /**
     * @var \ASPHP\Core\View\SelectListItem[]
     */
    protected $items = [];

    /**
     * @return \ASPHP\Core\View\SelectListItem[]
     */
    public function Items()
    {
        return $this->items;
    }

    public function AddItem(\ASPHP\Core\View\SelectListItem $item)
    {
        $this->items[] = $item;
        $this->isEmpty = false;
    }
}
?>