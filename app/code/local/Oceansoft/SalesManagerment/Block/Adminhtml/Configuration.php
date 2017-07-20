<?php

class Oceansoft_SalesManagerment_Block_Adminhtml_Configuration extends  Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'salesmanagerment';
        $this->_controller = 'adminhtml_configuration';
        $this->_headerText = $this->__('Configuration');

        parent::__construct();
    }
}