<?php
class Oceansoft_SalesManagerment_Model_Mysql4_Revenue_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('salesmanagerment/revenue');
    }
}