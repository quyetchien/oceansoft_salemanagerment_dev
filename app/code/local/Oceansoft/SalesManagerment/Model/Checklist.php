<?php
class Oceansoft_SalesManagerment_Model_Checklist extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('salesmanagerment/checklist');
    }

    public function getGroupSale()
    {
        $collection = Mage::getModel('salesmanagerment/salesreport')->getCollection()
            ->addFieldToFilter('checklist_id', $this->getId());
        if($collection){
            return $collection;
        }
        return false;
    }
}