<?php
namespace ASPHP\Core\View;
use \ASPHP\Core\View\SelectListGroup;
use \ASPHP\Core\View\SelectListItem;

class SelectList extends \ASPHP\Core\Types\ModelBase
{
    protected $hasGroups = false;
    public function __construct()
    {
        $this->groups = [];
        $this->groups[] = new SelectListGroup();
        $this->groups[0]->text = "";
    }

    public function HasGroups()
    {
        return $this->hasGroups;
    }

    protected $groups;

    public function AddItem(SelectListItem $item)
    {
        $this->groups[0]->AddItem($item);
    }

    public function AddGroup(SelectListGroup $group)
    {
        $this->groups[] = $group;
        $this->hasGroups = true;
    }

    public function Items()
    {
        return $this->groups;
    }
}
?>