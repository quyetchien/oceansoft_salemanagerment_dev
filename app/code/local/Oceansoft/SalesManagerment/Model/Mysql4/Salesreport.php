<?php
class Oceansoft_SalesManagerment_Model_Mysql4_Salesreport extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('salesmanagerment/salesreport', 'id');
    }
}