<?php
class Oceansoft_SalesManagerment_Block_Adminhtml_Configuration_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'salesmanagerment';
        $this->_controller = 'adminhtml_configuration';
        $this->_updateButton('save', 'label','Save Configuration');
        $this->_updateButton('delete', 'label', 'Delete Configuration');
    }


    public function getHeaderText()
    {
        if( Mage::registry('salesmanagerment_data') && Mage::registry('salesmanagerment_data')->getId() )
        {
            return 'Edit Configuration Id '.$this->htmlEscape( Mage::registry('salesmanagerment_data')->getId() );
        }
        else
        {
            return 'Add Configuration';
        }
    }
}