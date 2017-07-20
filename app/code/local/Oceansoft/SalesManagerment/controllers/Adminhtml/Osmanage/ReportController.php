<?php

class Oceansoft_SalesManagerment_Adminhtml_Osmanage_ReportController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Initialize action
     *
     * Here, we set the breadcrumbs and the active menu
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('salesmanagerment/salesreport')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Sales Report'), Mage::helper('adminhtml')->__('Sales Report')
            );
        return $this;
    }

    /**
     * export grid items to CSV file
     */
    public function exportCsvAction()
    {
        $fileName   = 'customersreport.csv';
        $content    = $this->getLayout()
            ->createBlock('rewardpointsreport/adminhtml_customer_grid')
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
            ->createBlock('rewardpointsreport/adminhtml_customer_grid')
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
            ->createBlock('rewardpointsreport/adminhtml_customer_grid')
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
        return Mage::getSingleton('admin/session')->isAllowed('rewardpoints/reports');
    }
}
