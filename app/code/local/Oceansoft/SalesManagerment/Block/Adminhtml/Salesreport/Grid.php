<?php

class Oceansoft_SalesManagerment_Block_Adminhtml_Salesreport_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('salesreportGrid');
        $this->setDefaultSort('user');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }


    protected function _prepareCollection()
    {
        $collection = Mage::getModel('salesmanagerment/checklist')->getCollection();

        // Add Form Filter Data
        $filter = Mage::app()->getRequest()->getParam('filter', null);
        $data   = array();
        if (is_string($filter)) {
            $data = Mage::helper('adminhtml')->prepareFilterString($filter);
        }
        if (isset($data['report_from'])) {
            $collection->addFieldToFilter('main_table.created_at', array(
                'from'  => $data['report_from'],
                'date'  => true
            ));
        }
        if (isset($data['report_to'])) {
            $collection->addFieldToFilter('main_table.created_at', array(
                'to'    => $data['report_to'],
                'date'  => true
            ));
        }

        // Add SUM columns
        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)
            ->columns(array(
                'user_id'   => 'user',
                'price' => 'SUM(price)',
                'total_salary' => 'SUM(total_earn)',
            ))->group(array('main_table.user'));

        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }


    protected function _prepareColumns()
    {
        $this->addColumn('user_id', array(
            'header'    => Mage::helper('salesmanagerment')->__('User Id'),
            'align'     => 'right',
            'width'     => '10px',
            'index'     => 'user_id',
            'type'      => 'text',
            'totals_label'  => Mage::helper('salesmanagerment')->__('Total'),
        ));

        $this->addColumn('username', array(
            'header'    => Mage::helper('salesmanagerment')->__('Username'),
            'align'     => 'right',
            'width'     => '100px',
            'renderer'  => 'salesmanagerment/adminhtml_salesreport_render_user',
        ));

        $this->addColumn('price', array(
            'header'    => Mage::helper('salesmanagerment')->__("Price"),
            'align'     => 'left',
            'index'     => 'price',
        ));

        $this->addColumn('total_salary', array(
            'header'    => Mage::helper('salesmanagerment')->__('Salary'),
            'renderer'  => 'salesmanagerment/adminhtml_salesreport_render_salary',
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('salesmanagerment')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('salesmanagerment')->__('XML'));
        $this->addExportType('*/*/exportExcel', Mage::helper('adminhtml')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    /**
     * get url for each row in grid
     *
     * @return string
     */
//    public function getRowUrl($row)
//    {
//        return $this->getUrl('adminhtml/customer/edit', array('id' => $row->getCustomerId()));
//    }

    /**
     * get grid url (use for ajax load)
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}
