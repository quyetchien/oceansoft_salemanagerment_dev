<?php

class Oceansoft_SalesManagerment_Adminhtml_Osmanage_ReportController extends Mage_Adminhtml_Controller_Action
{
    /**
     * index Action
     */
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('salesmanagerment/salesreport')
            ->_addBreadcrumb(
                $this->__('Ocean Sales'),
                $this->__('Ocean Sales')
            );
        $this->_title($this->__('Ocean Sales'))
            ->_title($this->__('Sales Report'));

        $this->getLayout()->getBlock('salesmanagerment.salesreport')
            ->setTitle($this->__('Ocean Sales Report'));

        $this->renderLayout();
    }

    /**
     * grid action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * export grid items to CSV file
     */
    public function exportCsvAction()
    {
        $fileName   = 'customersreport.csv';
        $content    = $this->getLayout()
            ->createBlock('salesmanagerment/adminhtml_salesreport_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid items to XML file
     */
    public function exportXmlAction()
    {
        $fileName   = 'customersreport.xml';
        $content    = $this->getLayout()
            ->createBlock('rewardpointsreport/adminhtml_salesreport_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid items to XML Excel file
     */
    public function exportExcelAction()
    {
        $fileName   = 'customersreport.xml';
        $content    = $this->getLayout()
            ->createBlock('rewardpointsreport/adminhtml_salesreport_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * get allowed report
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('salesmanagerment/salesreport');
    }
}
