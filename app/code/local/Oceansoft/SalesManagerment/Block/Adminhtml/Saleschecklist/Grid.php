<?php
class Oceansoft_SalesManagerment_Block_Adminhtml_Saleschecklist_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('id');
        $this->setId('sales_checklist_grid');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $user_id = 0;
        $session = Mage::getSingleton('admin/session');
        if($user = $session->getUser()){
            $user_id = $user->getUserId();
        }
        if(Mage::helper('salesmanagerment')->checkIsAdminUser($user_id)){
            $collection = Mage::getModel('salesmanagerment/checklist')
                ->getCollection();
        }else{
            $collection = Mage::getModel('salesmanagerment/checklist')
                ->getCollection()
                ->addFieldToFilter('user', $user_id);
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        // Add the columns that should appear in the grid
        $this->addColumn('id',
            array(
                'header'=> $this->__('ID'),
                'align' =>'right',
                'width' => '50px',
                'index' => 'id'
            )
        );

        $this->addColumn('order_id',
            array(
                'header'=> $this->__('Order Id'),
                'index' => 'order_id',
                'width' => '100px'
            )
        );

        $this->addColumn('customer_email',
            array(
                'header'=> $this->__('Customer Email'),
                'index' => 'customer_email'
            )
        );

        $this->addColumn('ticket_id',
            array(
                'header'=> $this->__('Ticket Id'),
                'index' => 'ticket_id'
            )
        );

        $this->addColumn('price',
            array(
                'header'=> $this->__('Price'),
                'index' => 'price',
                'type' => 'number',
            )
        );

        $this->addColumn('refund',
            array(
                'header'=> $this->__('Refund'),
                'renderer'  => 'salesmanagerment/adminhtml_saleschecklist_render_refund',
            )
        );

        $this->addColumn('sale_percentage',
            array(
                'header'=> $this->__('Sale Percentage'),
                'index' => 'sale_percentage'
            )
        );

        $this->addColumn('order_date',
            array(
                'header'=> $this->__('Order Date'),
                'index' => 'order_date',
                'type' => 'date',
                'format' => 'yyyy-MM-dd',
            )
        );

        $this->addColumn('shift',
            array(
                'header'=> $this->__('Shift'),
                'index' => 'shift'
            )
        );

        $this->addColumn('user',
            array(
                'header'=> $this->__('User'),
                'renderer'  => 'salesmanagerment/adminhtml_saleschecklist_render_user',
                'filter_condition_callback' => array($this, '_customUserFilter'),
            )
        );

        $this->addExportType('*/*/exportCsv', Mage::helper('salesmanagerment')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('salesmanagerment')->__('XML'));
        $this->addExportType('*/*/exportExcel', Mage::helper('adminhtml')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    public function styleDate( $value,$row,$column,$isExport )
    {
        $locale = Mage::app()->getLocale();
        $date = $locale->date( $value, $locale->getDateFormat(), $locale->getLocaleCode(), false )->toString( $locale->getDateFormat() ) ;
        return $date;
    }

    protected function _customUserFilter($collection, $column){
        if (!$value = $column->getFilter()->getValue()){
            return $this;
        }
        $user_id = Mage::helper('salesmanagerment')->getUserIdByName($value);
        $this->getCollection()->addFieldToFilter("user", $user_id);
        return $this;
    }

    public function getRowUrl($row)
    {
        // This is where our row data will link to
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}