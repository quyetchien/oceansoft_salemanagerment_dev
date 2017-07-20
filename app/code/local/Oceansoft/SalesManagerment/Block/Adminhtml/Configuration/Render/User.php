<?php

class Oceansoft_SalesManagerment_Block_Adminhtml_Configuration_Render_User extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $user_name = Mage::helper('salesmanagerment')->getUserNameById($row->getUserId());
        return $user_name;
    }
}