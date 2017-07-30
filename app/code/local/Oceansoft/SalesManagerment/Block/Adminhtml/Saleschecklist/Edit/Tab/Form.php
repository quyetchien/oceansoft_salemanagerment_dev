<?php

class Oceansoft_SalesManagerment_Block_Adminhtml_Saleschecklist_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('salesmanagerment_form',
            array('legend'=>'General Information'));

        $fieldset->addField('created_at', 'datetime',
            array(
                'label'     => 'Created At',
                'required'  => true,
                'name'      => 'created_at',
                'time'      => true,
                'format'    => $this->escDates(),
                'image'     => $this->getSkinUrl('images/grid-cal.gif')
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
                'required' => false,
                'name' => 'ticket_id',
            ));
        // custom field group
        $fieldset->addField('group', 'text', array(
            'name'      => 'group',
            'label'     => 'Group',
            'required'  => false,
        ));
        $group_sale = $form->getElement('group');
        $group_sale->setRenderer(
            $this->getLayout()->createBlock('salesmanagerment/adminhtml_saleschecklist_edit_renderer_group')
        );
        //
        $fieldset->addField('note', 'textarea',
            array(
                'label' => 'Note',
                'required' => false,
                'name' => 'note',
            ));
        $fieldset->addField('refund', 'text',
            array(
                'label' => 'Refunded',
                'required' => false,
                'name' => 'refund',
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
