<?php

class Oceansoft_SalesManagerment_Block_Adminhtml_Salesreport_Render_Salary extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $revenue =  $user_revenue = Mage::getModel('salesmanagerment/revenue')
            ->getCollection()
            ->addFieldToFilter('user_id', $row->getUserId());
        if($data_revenue = $revenue->getData()){
            $user_revenue = $data_revenue[0]['revenue'];
            if($user_revenue < $row->getPrice()){
                return $row->getTotalSalary();
            }else{
                return ($user_revenue - $row->getPrice()) * (-5) / 100;
            }
        }
        return 0;
    }
}