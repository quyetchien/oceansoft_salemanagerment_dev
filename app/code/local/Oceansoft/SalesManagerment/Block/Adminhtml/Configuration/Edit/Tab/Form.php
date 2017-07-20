<?php

class Oceansoft_SalesManagerment_Block_Adminhtml_Configuration_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('salesmanagerment_form',
            array('legend'=>'General Information'));

        $fieldset->addField('user_id', 'select',
            array(
                'label' => 'User',
                'class' => 'required-entry',
                'required' => true,
                'name' => 'user_id',
                'values' => Mage::helper('salesmanagerment')->getSaleUser()
            ));
        $fieldset->addField('revenue', 'text',
            array(
                'label' => 'Revenue',
                'class' => 'required-entry',
                'required' => true,
                'name' => 'revenue',
            ));
        $fieldset->addField('from', 'date',
            array(
                'label'     => 'From',
                'required'  => true,
                'name'      => 'from',
                'time'      => true,
                'format'    => $this->escDates(),
                'image'     => $this->getSkinUrl('images/grid-cal.gif')
            ));
        $fieldset->addField('to', 'date',
            array(
                'label'     => 'To',
                'required'  => true,
                'name'      => 'to',
                'time'      => true,
                'format'    => $this->escDates(),
                'image'     => $this->getSkinUrl('images/grid-cal.gif')
            ));


        if ( Mage::registry('salesmanagerment_data') )
        {
            $form->setValues(Mage::registry('salesmanagerment_data')->getData());
        }

        return parent::_prepareForm();
    }

    private function escDates() {
        return 'yyyy-MM-dd HH:mm:ss';
    }

}
