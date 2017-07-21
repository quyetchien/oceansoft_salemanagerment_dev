<?php
class Oceansoft_SalesManagerment_Block_Adminhtml_Revenue_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'salesmanagerment';
        $this->_controller = 'adminhtml_revenue';
        $this->_updateButton('save', 'label','Save Revenue');
        $this->_updateButton('delete', 'label', 'Delete Revenue');
    }


    public function getHeaderText()
    {
        if( Mage::registry('salesmanagerment_data') && Mage::registry('salesmanagerment_data')->getId() )
        {
            return 'Edit Revenue Id '.$this->htmlEscape( Mage::registry('salesmanagerment_data')->getId() );
        }
        else
        {
            return 'Add Revenue';
        }
    }
}