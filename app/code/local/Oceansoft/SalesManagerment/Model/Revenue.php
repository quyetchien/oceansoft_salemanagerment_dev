<?php
class Oceansoft_SalesManagerment_Model_Revenue extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('salesmanagerment/revenue');
    }

    public function getRevenueCollection()
    {
        $collection = Mage::getModel('salesmanagerment/revenue')->load($this->getId());
        if($collection){
            return $collection;
        }
        return false;
    }

}