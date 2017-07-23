<?php

class Oceansoft_SalesManagerment_Block_Adminhtml_Salesreport_Render_User extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $user = $row->getUserId();
        return Mage::helper('salesmanagerment')->getUserNameById($user);
    }
}