<?php
class Oceansoft_SalesManagerment_Block_Adminhtml_Saleseport extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_salesreport';
        $this->_blockGroup = 'salesmanagerment';
        $this->_headerText = Mage::helper('salesmanagerment')->__('Sales Report');
        parent::__construct();
        $this->_removeButton('add');
    }
}