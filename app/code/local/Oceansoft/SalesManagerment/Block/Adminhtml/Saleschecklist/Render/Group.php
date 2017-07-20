<?php

class Oceansoft_SalesManagerment_Block_Adminhtml_Saleschecklist_Render_Group extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $result = '';
        $groupCollection = Mage::getModel('salesmanagerment/checklistgroup')->getCollection()
            ->addFieldToFilter('checklist_id', $row->getId());
        if($groupData = $groupCollection->getData()){
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