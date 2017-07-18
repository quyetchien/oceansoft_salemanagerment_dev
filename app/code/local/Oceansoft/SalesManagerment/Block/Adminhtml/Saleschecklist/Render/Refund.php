<?php

class Oceansoft_SalesManagerment_Block_Adminhtml_Saleschecklist_Render_Refund extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $refund = $row->getRefund();
        return $refund . '%';
    }
}