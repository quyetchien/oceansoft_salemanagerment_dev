<?php

class Oceansoft_SalesManagerment_Block_Adminhtml_Saleschecklist_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('salesmanagerment_form',
            array('legend'=>'General Information'));

        if (Mage::registry('salesmanagerment_data')->getId()) {
            $fieldset->addField('order_date', 'date',
                array(
                    'label'     => 'Order Date',
                    'required'  => true,
                    'name'      => 'order_date',
                    'format' => 'yyyy-MM-dd',
                    'disabled' => true,
                ));
        }else{
            $fieldset->addField('order_date', 'date',
                array(
                    'label'     => 'Order Date',
                    'required'  => true,
                    'name'      => 'order_date',
                    'format' => 'yyyy-MM-dd',
                    'image'     => $this->getSkinUrl('images/grid-cal.gif')
                ));
        }
        $fieldset->addField('shift', 'select',
            array(
                'label' => 'Shift',
                'class' => 'required-entry',
                'required' => true,
                'name' => 'shift',
                'values' => $this->_getListShiftForUser(),
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
                'required' => true,
                'name' => 'ticket_id',
            ));
        $fieldset->addField('sale_percentage', 'select',
            array(
                'label' => 'Sale Percentage',
                'class' => 'required-entry',
                'required' => true,
                'name' => 'sale_percentage',
                'values' => $this->_listPercentage(),
            ));
        $fieldset->addField('note', 'textarea',
            array(
                'label' => 'Note',
                'required' => false,
                'name' => 'note',
            ));
        $fieldset->addField('refund', 'select',
            array(
                'label' => 'Refunded',
                'required' => false,
                'name' => 'refund',
                'values' => $this->_listPercentage(true),
            ));
        $fieldset->addField('refund_reason', 'textarea',
            array(
                'label' => 'Refund Reason',
                'required' => false,
                'name' => 'refund_reason',
            ));

        if ( Mage::registry('salesmanagerment_data') )
        {
            $form->setValues(Mage::registry('salesmanagerment_data')->getData());
        }

        return parent::_prepareForm();
    }

    protected function _listPercentage($emptyValue = false){
        $result = array();
        if($emptyValue){
            $result[0] = '';
        }
        $i = 10;
        while ($i <= 100){
            $result[$i] = $i . '%';
            $i = $i + 10;
        }
        return $result;
    }

    protected function _getListShiftForUser(){
        $checklist_id = $this->getRequest()->getParam('id');
        $collection = Mage::getModel('salesmanagerment/checklist')->load($checklist_id);
        if($collectionData = $collection->getData()){
            $user_id = $collectionData['user'];
        }else{
            $session = Mage::getSingleton('admin/session');
            $user_id = 0;
            if($user = $session->getUser()){
                $user_id = $user->getUserId();
            }
        }
        $result = array();
        $revenue = Mage::getModel('salesmanagerment/revenue')->getCollection()
            ->addFieldToFilter('user_id', $user_id);
        if($revenueData = $revenue->getData()){
            if($revenueData[0]['rule']){
                $userRule = unserialize($revenueData[0]['rule']);
                if($userRule){
                    foreach($userRule as $rule){
                        $result[$rule['shift']] = 'Ca ' . $rule['shift'];
                    }
                }
            }
        }
        return $result;
    }
}
