<?php

class Oceansoft_SalesManagerment_Block_Adminhtml_Saleschecklist extends  Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'salesmanagerment';
        $this->_controller = 'adminhtml_saleschecklist';
        $this->_headerText = $this->__('Sales Checklist');

        parent::__construct();
    }
}