<?php

class Oceansoft_SalesManagerment_Block_Adminhtml_Saleschecklist_Render_Group extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $result = '';
        $checklistCollection = Mage::getModel('salesmanagerment/salesreport')->getCollection()
            ->addFieldToFilter('order_id', $row->getOrderId());
        if($groupData = $checklistCollection->getData()){
            foreach($groupData as $key => $group_data){
                $user_name = Mage::helper('salesmanagerment')->getUserNameById($group_data['user_id']);
                if($key == 0){
                    $result .= $user_name . ": " . $group_data['value'] . '%';
                }else{
                    $result .= "<br>" . $user_name . ": " . $group_data['value'] . '%';
                }
            }
        }
        return $result;
    }
}