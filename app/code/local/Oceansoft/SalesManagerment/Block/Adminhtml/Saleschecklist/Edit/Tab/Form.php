<?php

class Oceansoft_SalesManagerment_Block_Adminhtml_Saleschecklist_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $this->setForm($form);
        $fieldset = $form->addFieldset('salesmanagerment_form',
            array('legend'=>'General Information'));
        $fieldset->addField('order_date', 'date',
            array(
                'label' => 'Time',
                'class' => 'required-entry',
                'required' => true,
                'input_format' => $dateFormatIso,
                'format'       => $dateFormatIso,
                'time'      => true,
                'image'     =>  $this->getSkinUrl('images/grid-cal.gif'),
                'name' => 'order_date',
            ));
        $fieldset->addField('order_id', 'text',
            array(
                'label' => 'Order Id',
                'class' => 'required-entry',
                'required' => true,
                'name' => 'order_id',
            ));
        $fieldset->addField('ticket_id', 'text',
            array(
                'label' => 'Ticket Id',
                'class' => 'required-entry',
                'required' => true,
                'name' => 'ticket_id',
            ));
        // custom field group
        $groupSales_field = $fieldset->addField('group', 'text', array(
            'name'      => 'group',
            'label'     => 'Group',
            'required'  => false,
        ));

        $group_sale = $form->getElement('group');

        $group_sale->setRenderer(
            $this->getLayout()->createBlock('salesmanagerment/adminhtml_saleschecklist_edit_renderer_group')
        );


        if ( Mage::registry('rewardpointscronemails_data') )
        {
            $form->setValues(Mage::registry('rewardpointscronemails_data')->getData());
        }

        return parent::_prepareForm();
    }

}
