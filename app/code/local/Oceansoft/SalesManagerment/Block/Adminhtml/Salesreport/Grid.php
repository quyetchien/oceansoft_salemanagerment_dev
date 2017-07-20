<?php
class Oceansoft_SalesManagerment_Block_Adminhtml_Salesreport_Grid extends Mage_Adminhtml_Block_Report_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('sales_report_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setSubReportSize(false);
    }

    protected function _prepareCollection() {
        parent::_prepareCollection();
        $this->getCollection()->initReport('salesmanagerment/salesreport');
        return $this;
    }

    protected function _prepareColumns() {
        $this->addColumn('ordered_qty', array(
            'header'    =>Mage::helper('salesmanagerment')->__('Quantity Ordered'),
            'align'     =>'right',
            'index'     =>'ordered_qty',
            'total'     =>'sum',
            'type'      =>'number'
        ));
        $this->addColumn('item_id', array(
            'header' => $this->__('Item ID'),
            'align' => 'right',
            'index' => 'item_id',
            'type'  => 'number',
            'total' => 'sum',
        ));
        $this->addExportType('*/*/exportCsv', Mage::helper('salesmanagerment')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('salesmanagerment')->__('XML'));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return false;
    }

    public function getReport($from, $to) {
        if ($from == '') {
            $from = $this->getFilter('report_from');
        }
        if ($to == '') {
            $to = $this->getFilter('report_to');
        }
        $totalObj = Mage::getModel('reports/totals');
        $totals = $totalObj->countTotals($this, $from, $to);
        $this->setTotals($totals);
        $this->addGrandTotals($totals);
        return $this->getCollection()->getReport($from, $to);
    }
}