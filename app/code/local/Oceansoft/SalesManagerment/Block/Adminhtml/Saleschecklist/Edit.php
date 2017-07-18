<?php
class Oceansoft_SalesManagerment_Block_Adminhtml_Saleschecklist_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'salesmanagerment';
        $this->_controller = 'adminhtml_saleschecklist';
        $this->_updateButton('save', 'label','Save Note');
        $this->_updateButton('delete', 'label', 'Delete Note');
    }


    public function getHeaderText()
    {
        if( Mage::registry('salesmanagerment_data') && Mage::registry('salesmanagerment_data')->getId() )
        {
            return 'Edit Note Order Id '.$this->htmlEscape( Mage::registry('salesmanagerment_data')->getOrderId() );
        }
        else
        {
            return 'Add Note';
        }
    }
}