<?php

class Oceansoft_SalesManagerment_Block_Adminhtml_Revenue_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('salesmanagerment_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle('Revenue Information');
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label' => 'General Information',
            'title' => 'General Information',
            'content' => $this->getLayout()
                ->createBlock('salesmanagerment/adminhtml_revenue_edit_tab_form')
                ->toHtml()
        ));

        return parent::_beforeToHtml();
    }
}