<?php

class Oceansoft_SalesManagerment_Block_Adminhtml_Revenue extends  Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'salesmanagerment';
        $this->_controller = 'adminhtml_revenue';
        $this->_headerText = $this->__('Revenue');

        parent::__construct();
    }
}