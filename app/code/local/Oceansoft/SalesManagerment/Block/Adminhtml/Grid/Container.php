<?php

class Oceansoft_SalesManagerment_Block_Adminhtml_Grid_Container extends Mage_Adminhtml_Block_Template
{
    /**
     * get input date format
     *
     * @return string
     */
    public function getDateFormat()
    {
        return Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
    }

    /**
     * get current filter value by name
     *
     * @param string $filterName
     * @return mixed
     */
    public function getFilter($filterName)
    {
        $filter = Mage::app()->getRequest()->getParam('filter');
        $data   = Mage::helper('adminhtml')->prepareFilterString($filter);
        return isset($data[$filterName]) ? $data[$filterName] : null;
    }

    /**
     * get js grid object name
     *
     * @return string
     */
    public function getJsObjectName()
    {
        $gridBlock = $this->getChild('grid_content');
        return $gridBlock ? $gridBlock->getJsObjectName() : '';
    }
}
